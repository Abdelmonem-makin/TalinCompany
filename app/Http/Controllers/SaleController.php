<?php

namespace App\Http\Controllers;

use App\Http\Requests\salesRequest;
use App\Models\Sales;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Transaction;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:sales_read'])->only('index');
        $this->middleware(['permission:sales_read'])->only('show');
        $this->middleware(['permission:sales_create'])->only('create');
        $this->middleware(['permission:sales_create'])->only('store');
        $this->middleware(['permission:sales_update'])->only('edit');
        $this->middleware(['permission:sales_update'])->only('update');
        $this->middleware(['permission:sales_delete'])->only('destroy');
    }
    public function index(Request $request)
    {
        $search = $request->get('search');
        $customers = Customer::pluck('name', 'id');
        // Only load items that have non-expired remaining stock
        $items = Item::with('Stocks')

            // whereHas('Stocks', function($q){
            //         $q->where(function($q2){
            //             $q2->whereNull('is_expired')->orWhere('is_expired', false);
            //         })->where('remaining', '>', 0);
            //     })->with('supplier')

            ->latest()->paginate(5);
        // compute available non-expired remaining per item for UI decisions
        foreach ($items as $it) {
            $it->available = Stock::where('item_id', $it->id)
                ->where(function ($q) {
                    $q->whereNull('is_expired')->orWhere('is_expired', false);
                })
                ->where('remaining', '>', 0)
                ->sum('remaining');
        }
        $sales = Sales::with('Stock')->when($search, function ($query) use ($search) {
            return $query->where('invoice_number', 'like', '%' . $search . '%')
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
        })->latest()->paginate(15)->appends(['search' => $search]);
        return view('sales.index', compact('sales', 'customers', 'items', 'search'));
    }


    public function store(salesRequest $request)
    {
        // try {
        $validator = FacadesValidator::make($request->all(), []);
        if ($validator->fails()) {
            return redirect()->back()->withErrors(['خطأ في بيانات الطلب', 'errors' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        $total_price = 0;
        $lastInvoice = Sales::orderBy('id', 'desc')->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        $invoiceNumber = 'INV-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $Sales = Sales::create([
            'user_id' =>  Auth::user()->id,
            'invoice_number' => $invoiceNumber,
            'date' => now(),
            // 'status' => 'panding',
            'customer_id' => $request->customer_id,
        ]);

        // Attach products with explicit pivot data (stock and price)
        $attachData = [];
        foreach ($request->products as $id => $quantities) {
            $Item = Item::findOrFail($id);
            // ensure available non-expired stock across batches is sufficient
            $available = \App\Models\Stock::where('item_id', $Item->id)
                ->where(function ($q) {
                    $q->whereNull('is_expired')->orWhere('is_expired', false);
                })
                ->where(function ($q) {
                    $q->whereNull('expiry')->orWhereDate('expiry', '>=', now()->toDateString());
                })
                ->where('status', 'confirm')
                ->sum('quantity');

            if ($available < $quantities['quantity']) {
                DB::rollBack();
                return redirect()->back()->withErrors(['amount' =>  "لا يمكن بيع منتج منتهي الصلاحية أو الكمية غير كافية"]);
            }
            $lineTotal = $Item->price * $quantities['quantity'];
            $total_price += $lineTotal;
            $attachData[$id] = [
                'stock' => $quantities['quantity'],
                'sales_price' => $lineTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // decrement item total stock
            $Item->decrement('stock', $quantities['quantity']);

            // Allocate quantity from non-expired batches (FIFO by expiry)
            $needed = intval($quantities['quantity']);
            $batches = Stock::where('item_id', $Item->id)
                ->whereDate('expiry', '>=', now()->toDateString())
                ->where(function ($q) {
                    $q->whereNull('is_expired')->orWhere('is_expired', 0);
                })
                ->where('quantity', '>', 0)
                ->orderBy('expiry', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($batches as $batch) {
                if ($needed <= 0) break;
                $take = min($batch->quantity ?? 0, $needed);
                if ($take <= 0) continue;

                // reduce batch remaining
                $batch->quantity = max(0, ($batch->quantity ?? 0) - $take);
                if ($batch->quantity == 0) {
                    $batch->status = 'sold';
                }
                $batch->save();

                // create stock movement linking to this purchase batch
                Stock::create([
                    'item_id' => $Item->id,
                    'quantity' => -1 * $take,
                    'type' => 'مبيعات',
                    'reference_id' => $Sales->id,
                    'sale_id' => $Sales->id,
                    'purchase_id' => $batch->purchase_id ?? null,
                    'batch_id' => $batch->id,
                    'status' => 'sold',
                    'note' => 'تم بيع ' . $take . ' من ' . ($Item->name ?? '') . ' من الدفعة #' . $batch->id,
                    'customer_id' => $Sales->customer_id ?? null,
                ]);

                $needed -= $take;
            }

            if ($needed > 0) {
                DB::rollBack();
                return redirect()->back()->withErrors(["الكمية المطلوبة غير متوفرة بعد تخصيص الدفعات:"]);
            }
        }
        $Sales->item()->attach($attachData);
        $Sales->update([
            'total' => $total_price,
        ]);


        $this->createSalesBankingEntries($Sales, $total_price);
        DB::commit();

        return redirect()->back()->with('success', 'تم الشراء بنجاح');
        // return response()->json(['success' => true, 'message' => 'تم الشراء بنجاح']);
        // } catch (\Throwable $th) {
        //     Log::error('Sales store error', ['exception' => (string) $th]);
        //     return redirect()->back()->withErrors([ 'حدث خطأ أثناء إنشاء الفاتورة، يرجى المحاولة لاحقًا.']);
        // }
    }
    protected function createSalesBankingEntries(Sales $sale, $amount)
    {
        $date = Carbon::now()->toDateString();

        // Customer account or fallback to "Accounts Receivable"
        $customerBank = null;
        if ($sale->customer) {
            $customerBank = Bank::find($sale->customer->account_id);
        } else {

            $customerBank = Bank::firstOrCreate(['name' =>  'عميل افتراضي'], ['type' => 'asset', 'number' => 'customer', 'balance' => 0, 'kind' => 'receipt']);
        }
        // Revenue account
        $revenueBank = Bank::firstOrCreate(
            ['name' => 'ايرادات المبيعات'],
            ['type' => 'revenue', 'number' => "ac-revenue", 'balance' => 0]
        );
        if (!$customerBank) {
            $customerBank = Bank::firstOrCreate(
                ['name' => $sale->customer->name],
                ['type' => 'asset', 'number' => 'customer', 'balance' => 0, 'kind' => 'receipt']
            );
            $sale->customer->update(['account_id' => $customerBank->id]);
            $sale->save();
        }



        // Debit customer (increase asset)
        $this->createTransactionAndAdjustBalance($customerBank, $amount, 'debit', " فاتورة مبيعات {$sale->invoice_number}", $date);

        // Credit revenue (increase income)
        $this->createTransactionAndAdjustBalance($revenueBank, $amount, 'credit', "فاتورة مبيعات {$sale->invoice_number}", $date);
    }

    function print_salas_order($id)
    {

        $sales_pro = Sales::find($id);
        $Sales  = $sales_pro->item;
        $sales = Sales::with('item')->find($id);
        $sales_pro = $sales->item;
        return view('sales.show', compact('sales_pro', 'sales'));
    }
    public function show(Sales $sale)
    {
        $items = \App\Models\Item::pluck('name', 'id');
        return view('sales.show', compact('sale', 'items'));
    }
    public function getSaleData(Sales $sale)
    {
        $lines = $sale->item->map(function ($item) {
            return [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'quantity' => $item->pivot->stock,
                'unit_price' => $item->price,
                'total' => $item->pivot->sales_price,
            ];
        });

        return response()->json([
            'invoice_number' => $sale->invoice_number,
            'customer_id' => $sale->customer_id,
            'customer' => optional($sale->customer)->name,
            'date' => $sale->date ? $sale->date->format('Y-m-d') : now()->format('Y-m-d'),
            'total' => $sale->total,
            'lines' => $lines,
        ]);
    }

    public function update(Request $request, Sales $sale)
    {
        // Make invoices immutable after creation
        return redirect()->back()->withErrors('الفاتورة غير قابلة للتعديل بعد إنشائها.');

        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'nullable|date',
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Step 1: Reverse old stock changes and accounting entries
            foreach ($sale->item as $item) {
                $quantity = $item->pivot->stock;
                $item->increment('stock', $quantity);
                Stock::where('reference_id', $sale->id)->where('item_id', $item->id)->delete();
            }

            // Reverse old accounting entries
            $oldTransactions = Transaction::where('description', 'like', "فاتورة مبيعات {$sale->invoice_number}%")->get();
            foreach ($oldTransactions as $transaction) {
                $bank = Bank::find($transaction->account_id);
                if ($transaction->type === 'debit') {
                    $bank->balance -= $transaction->amount;
                } else {
                    $bank->balance += $transaction->amount;
                }
                $bank->save();
                $transaction->delete();
            }

            // Step 2: Update sales record
            $sale->update([
                'customer_id' => $request->customer_id,
                'date' => $request->date ?? now(),
            ]);

            // Step 3: Detach old items and attach new ones
            $sale->item()->detach();
            $total_price = 0;
            $attachData = [];
            foreach ($request->products as $id => $quantities) {
                $Item = Item::findOrFail($id);
                $available = \App\Models\Stock::where('item_id', $Item->id)
                    ->where(function ($q) {
                        $q->where('is_expired', false)->orWhereNull('is_expired');
                    })
                    ->sum('remaining');
                if ($available < $quantities['quantity']) {
                    DB::rollBack();
                    return redirect()->back()->withErrors("الكمية المتاحة غير كافية من المنتج {$Item->name} (بما في ذلك الدفعات غير منتهية الصلاحية)");
                }
                $lineTotal = $Item->price * $quantities['quantity'];
                $total_price += $lineTotal;
                $attachData[$id] = [
                    'stock' => $quantities['quantity'],
                    'sales_price' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Decrement stock and create stock entry
                $Item->decrement('stock', $quantities['quantity']);
                Stock::create([
                    'item_id' => $Item->id,
                    'quantity' => -1 * $quantities['quantity'],
                    'type' => 'مبيعات',
                    'reference_id' => $sale->id,
                    'sale_id' => $sale->id,
                    'status' => 'draft',
                    'note' => 'تم بيع ' . $Item->name,
                ]);
            }
            $sale->item()->attach($attachData);

            // Step 4: Update total
            $sale->update(['total' => $total_price]);

            // Step 5: Create new accounting entries
            $this->createSalesBankingEntries($sale, $total_price);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error updating sale: ' . $e->getMessage());
        }
    }

    public function updateSales(Request $request, Sales $sale)
    {
        // Prevent editing sales - invoices are immutable
        return redirect()->back()->withErrors('الفاتورة غير قابلة للتعديل بعد إنشائها.');

        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'nullable|date',
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Step 1: Reverse old stock changes and accounting entries
            foreach ($sale->item as $item) {
                $quantity = $item->pivot->stock;
                $item->increment('stock', $quantity);
                Stock::where('reference_id', $sale->id)->where('item_id', $item->id)->delete();
            }

            // Reverse old accounting entries
            $oldTransactions = Transaction::where('description', 'like', "فاتورة مبيعات {$sale->invoice_number}%")->get();
            foreach ($oldTransactions as $transaction) {
                $bank = Bank::find($transaction->account_id);
                if ($transaction->type === 'debit') {
                    $bank->balance -= $transaction->amount;
                } else {
                    $bank->balance += $transaction->amount;
                }
                $bank->save();
                $transaction->delete();
            }

            // Step 2: Update sales record
            $sale->update([
                'customer_id' => $request->customer_id,
                'date' => $request->date ?? now(),
            ]);

            // Step 3: Detach old items and attach new ones
            $sale->item()->detach();
            $total_price = 0;
            $attachData = [];
            foreach ($request->products as $id => $quantities) {
                $Item = Item::findOrFail($id);
                $available = \App\Models\Stock::where('item_id', $Item->id)
                    ->where(function ($q) {
                        $q->where('is_expired', false)->orWhereNull('is_expired');
                    })
                    ->sum('remaining');
                if ($available < $quantities['quantity']) {
                    DB::rollBack();
                    return redirect()->back()->withErrors("الكمية المتاحة غير كافية من المنتج {$Item->name} (بما في ذلك الدفعات غير منتهية الصلاحية)");
                }
                $lineTotal = $Item->price * $quantities['quantity'];
                $total_price += $lineTotal;
                $attachData[$id] = [
                    'stock' => $quantities['quantity'],
                    'sales_price' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Decrement stock and create stock entry
                $Item->decrement('stock', $quantities['quantity']);
                Stock::create([
                    'item_id' => $Item->id,
                    'quantity' => -1 * $quantities['quantity'],
                    'type' => 'مبيعات',
                    'reference_id' => $sale->id,
                    'sale_id' => $sale->id,
                    'status' => 'draft',
                    'note' => 'تم بيع ' . $Item->name,
                ]);
            }
            $sale->item()->attach($attachData);
            if ($total_price >  $sale->total) {
                $old_total = $total_price - $sale->total;
            }
            if ($total_price <  $sale->total) {
                $old_total =  $total_price - $sale->total;
            }
            // Step 4: Update total
            $sale->update(['total' => $total_price]);

            // Step 5: Create new accounting entries
            $this->createSalesBankingEntries($sale, $old_total);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error updating sale: ' . $e->getMessage());
        }
    }

    public function destroy(Sales $sale)
    {
        // optional: restore stock from lines
        foreach ($sale->item as $line) {
            $line->increment('stock', $line->pivot->stock);
            $line->pivot->delete();
        }
        foreach ($sale->Stock as $Stock) {
            $Stock->delete();
        }
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted.');
    }

    protected function createTransactionAndAdjustBalance(bank $bank, $amount, $side, $description = null, $date = null)
    {
        $date = $date ?? Carbon::now()->toDateString();

        Transaction::create([
            'account_id' => $bank->id,
            'amount' => $amount,
            'type' => $side,
            'description' => $description,
            'date' => $date,
        ]);

        // Adjust balance according to simple accounting rules
        $debitIncrease = ['asset', 'bank', 'CASH', 'expense'];
        $creditIncrease = ['liability', 'equity', 'revenue', 'income'];

        if ($side === 'debit') {
            $bank->balance = in_array($bank->type, $debitIncrease)
                ? ($bank->balance ?? 0) + $amount
                : ($bank->balance ?? 0) - $amount;
        } else {
            $bank->balance = in_array($bank->type, $creditIncrease)
                ? ($bank->balance ?? 0) + $amount
                : ($bank->balance ?? 0) - $amount;
        }


        $bank->save();
    }
}

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
use Illuminate\Support\Facades\Validator as FacadesValidator;

class SaleController extends Controller
{
    public function index()
    {
        $customers = Customer::pluck('name', 'id');

        $sales = Sales::latest()->paginate(15);
        return view('sales.index', compact('sales' , 'customers'));
    }

    public function create()
    {
        $items = Item::latest()->paginate(15);
        $customers = Customer::get();
        return view('sales.create', compact('customers', 'items'));
    }

    public function store(salesRequest $request)
    {
        // try {
        $validator = FacadesValidator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json(['error' => false, 'message' => 'خطأ في بيانات الطلب', 'errors' => $validator->errors()], 422);
        }

        $total_price = 0;
        $lastInvoice = Sales::orderBy('id', 'desc')->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        $invoiceNumber = 'INV-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $Sales = Sales::create([
            'user_id' =>  Auth::user()->id,
            'invoice_number' => $invoiceNumber,
            'date' => now(),
            'status' => 'panding',
            'customer_id' => $request->customer_id,
        ]);

        // Attach products with explicit pivot data (stock and price)
        $attachData = [];
        foreach ($request->products as $id => $quantities) {
            $Item = Item::findOrFail($id);
            if ($Item->stock < $quantities['quantity']) {
                // DB::rollBack();
                return redirect()->back()->withErrors( "الكمية المتاحة من المنتج {$Item->name} أقل من المطلوب");
            }
            $lineTotal = $Item->price * $quantities['quantity'];
            $total_price += $lineTotal;
            $attachData[$id] = [
                'stock' => $quantities['quantity'],
                'sales_price' => $lineTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // decrement stock
            // $Item->decrement('stock', $quantities['quantity']);
            Stock::create([
                'item_id' => $Item->id,
                'change' => -1 * $quantities['quantity'],
                'type' => 'مبيعات',
                'reference_id' => $Sales->id,
                'note' => 'تم بيع ' . $Item->name,
            ]);
        }
        $Sales->item()->attach($attachData);
        $Sales->update([
            'total' => $total_price,
        ]);


        $this->createSalesBankingEntries($Sales, $total_price);

        return redirect()->back()->with('success', 'تم الشراء بنجاح');
        // return response()->json(['success' => true, 'message' => 'تم الشراء بنجاح']);
        // } catch (\Throwable $th) {
        //            return redirect()->back()->with('error', $th);
        //     // return response()->json(['error' => true, 'message' =>  $th]);
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

            $customerBank = Bank::firstOrCreate(['name' =>  'عميل افتراضي'], ['type' => 'asset', 'number' => 'CU-ACCUNT-01', 'balance' => 0, 'kind' => 'receipt']);
        }
        // Revenue account
        $revenueBank = Bank::firstOrCreate(
            ['name' => 'ايرادات المبيعات'],
            ['type' => 'revenue', 'number' => "ct-{{$customerBank}}", 'balance' => 0]
        );
        if (!$customerBank) {
            $customerBank = Bank::firstOrCreate(
                ['name' => $sale->customer->name],
                ['type' => 'asset', 'number' => 'AR', 'balance' => 0, 'kind' => 'receipt']
            );
            $sale->customer->update(['account_id' => $customerBank->id]);
            $sale->save();
        }



        // Debit customer (increase asset)
        $this->createTransactionAndAdjustBalance($customerBank, $amount, 'debit', " فاتورة مبيعات {$sale->invoice_number}", $date);

        // Credit revenue (increase income)
        $this->createTransactionAndAdjustBalance($revenueBank, $amount, 'credit', "فاتورة مبيعات {$sale->invoice_number}", $date);
    }

    function show_sales_order($id)
    {
        $sales = Sales::with('item')->find($id);
        $sales_pro = $sales->item;
        return view('sales.show', compact('sales_pro', 'sales'));
    }
    public function show(Sales $sale)
    {
        $items = \App\Models\Item::pluck('name', 'id');
        return view('sales.show', compact('sale', 'items'));
    }

    public function edit(Sales $sale)
    {
        $customers = Customer::pluck('name', 'id');
        return view('sales.edit', compact('sale', 'customers'));
    }

    public function update(Request $request, Sales $sale)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'nullable|date',
            'total' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);

        $sale->update($data);
        return redirect()->route('sales.index')->with('success', 'Sale updated.');
    }

    public function destroy(Sales $sale)
    {
        // optional: restore stock from lines
        foreach ($sale->item as $line) {

            $line->increment('stock', $line->pivot->stock);

            $line->pivot->delete();
        }

        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted.');
    }

    // public function confirm(Sales $sale)
    // {
    //     // Require authenticated user and simple 'is_accountant' flag on user
    //     $user = Auth::user();
    //     if (! $user || ! ($user->is_accountant ?? false)) {
    //         abort(403, 'Only accountants can confirm sales.');
    //     }

    //     if ($sale->status === 'confirmed') {
    //         return redirect()->back()->with('info', 'Sale already confirmed.');
    //     }

    //     // Recalculate total and persist
    //     $sale->total = $sale->lines()->sum('total');
    //     $sale->status = 'confirmed';
    //     $sale->save();

    //     // For each line, decrement stock and create stock movements
    //     foreach ($sale->lines as $line) {
    //         $item = $line->item;
    //         if ($item) {
    //             $item->decrement('stock', $line->stock);
    //             Stock::create([
    //                 'item_id' => $item->id,
    //                 'change' => -1 * $line->stock,
    //                 'type' => 'sale_confirm',
    //                 'reference_id' => $line->id,
    //                 'note' => "Sale #{$sale->id} confirmed",
    //             ]);
    //         }
    //     }

    //     // Create accounting entries for the whole sale
    //     // $this->createSaleAccountingEntries($sale);

    //     return redirect()->route('sales.show', $sale)->with('success', 'Sale confirmed by accountant.');
    // }

    // protected function createSaleAccountingEntries(Sales $sale)
    // {
    //     $date = Carbon::now()->toDateString();

    //     // determine customer account (linked) or fallback to 'Accounts Receivable'
    //     $customerAccount = null;
    //     if ($sale->customer && $sale->customer->account_id) {
    //         $customerAccount = Account::find($sale->customer->account_id);
    //     }

    //     if (! $customerAccount) {
    //         $customerAccount = Account::firstOrCreate(
    //             ['name' => 'Accounts Receivable'],
    //             ['type' => 'asset', 'number' => 'AR', 'balance' => 0]
    //         );
    //     }

    //     // determine revenue account (Sales Revenue)
    //     $revenueAccount = Account::firstOrCreate(
    //         ['name' => 'Sales Revenue'],
    //         ['type' => 'revenue', 'number' => 'SALES', 'balance' => 0]
    //     );

    //     $amount = $sale->total;

    //     // Debit customer (increase AR)
    //     $this->createTransactionAndAdjustBalance($customerAccount, $amount, 'debit', "Sale #{$sale->id}", $date);

    //     // Credit revenue
    //     $this->createTransactionAndAdjustBalance($revenueAccount, $amount, 'credit', "Sale #{$sale->id}", $date);
    // }

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

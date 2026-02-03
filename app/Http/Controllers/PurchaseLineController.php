<?php

namespace App\Http\Controllers;

use App\Models\PurchaseLine;
use App\Models\Purchases;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Bank;
use App\Models\Stock as ModelsStock;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseLineController extends Controller
{
    public function store(Request $request, Supplier $Supplier)
    {
        $data = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $data['total'] = $data['quantity'] * $data['unit_price'];

        $line = PurchaseLine::create($data);

        // increment item stock
        $item = Item::find($data['item_id']);
        if ($item) {
            // $item->increment('stock', $data['quantity']);
            Stock::create([
                'purchase_id' => $data['purchase_id'],
                'item_id' => $item->id,
                'change' => $data['quantity'],
                'type' => 'مشتريات',
                'reference_id' => $line->id,
                'note' => '   تم شراء  ' . $item->name,
            ]);
        }

        // update purchase total
        $purchase = Purchases::find($data['purchase_id']);
        if ($purchase) {
            $purchase->total = $purchase->lines()->sum('total');
            $purchase->save();

            // create Banking entries for the purchase line
            $this->createPurchaseBankingEntries($purchase, $line->total, $line->id);
        }

        return redirect()->back()->with('success', ' تم اضافة صنف بنجاح.');
    }

    public function storeFull(Request $request)
    {
        // dd($request->all()); // للاختبار
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json(['error' => false, 'message' => 'خطأ في بيانات الطلب', 'errors' => $validator->errors()], 422);
        }

        try {
            $total_price = 0;
            $lastPurchase = Purchases::orderBy('id', 'desc')->first();
            $nextId = $lastPurchase ? $lastPurchase->id + 1 : 1;
            $purchaseNumber = 'PUR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $purchase = Purchases::create([
                'reference_id' => $purchaseNumber,
                'supplier_id' => $request->supplier_id,
                'date' => now(),
            ]);

            // Create purchase lines
            foreach ($request->products as $id => $quantities) {
                $item = Item::findOrFail($id);
                $lineTotal = $quantities['unit_price'] * $quantities['quantity'];
                $total_price += $lineTotal;
                $line = PurchaseLine::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $id,
                    'quantity' => $quantities['quantity'],
                    'unit_price' => $quantities['unit_price'],
                    'total' => $lineTotal,
                ]);

                // Update item stock
                $item->increment('stock', $quantities['quantity']);

                Stock::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item->id,
                    'quantity' => $quantities['quantity'],
                    'type' => 'مشتريات',
                    'reference_id' => $purchase->id,
                    'status' => 'draft',
                    'note' => '   تم شراء  ' . $item->name .  ' من المورد ' .  optional($purchase->supplier)->name
                ]);
            }
            $purchase->update([
                'total' => $total_price,
            ]);
            $this->createPurchaseBankingEntries($purchase, $total_price, $purchase->id);

            return redirect()->route('purchases.index', $purchase)->with('success', 'تم إضافة المشتريات بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function updateFull(Request $request, Purchases $purchase)
    {
        try {
            // Get old total for reversal

            // Reverse old stock for existing lines
            foreach ($purchase->lines as $oldLine) {
                $oldItem = $oldLine->item;
                if ($oldItem) {
                    $oldItem->decrement('stock', $oldLine->quantity);
                }
            }

            // Delete existing lines
            $purchase->lines()->delete();

            $total_price = 0;
            $purchase->update([
                'supplier_id' => $request->supplier_id,
            ]);
            Stock::where('purchase_id', $purchase->id)->delete();

            // Create new purchase lines
            foreach ($request->products as $id => $quantities) {
                $item = Item::findOrFail($id);
                $lineTotal = $quantities['unit_price'] * $quantities['quantity'];
                $total_price += $lineTotal;
                $line = PurchaseLine::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $id,
                    'quantity' => $quantities['quantity'],
                    'unit_price' => $quantities['unit_price'],
                    'total' => $lineTotal,
                ]);

                // Update item stock
                $item->increment('stock', $quantities['quantity']);

                // Create stock entry for new line
                Stock::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item->id,
                    'quantity' => $quantities['quantity'],
                    'type' => 'مشتريات',
                    'reference_id' => $purchase->id,
                    'status' => 'draft',
                    'note' => 'تم شراء ' . $item->name . ' من المورد ' . optional($purchase->supplier)->name
                ]);
            }

            if ($total_price >  $purchase->total) {
                $old_total = $total_price - $purchase->total;
            }
            if ($total_price <  $purchase->total) {
                $old_total =  $total_price - $purchase->total;
            }
            $purchase->update([
                'total' => $total_price,
            ]);

            // Create new banking entries with updated total
            $this->createPurchaseBankingEntries($purchase, $old_total, $purchase->id);

            return redirect()->route('purchases.index', $purchase)->with('success', 'تم تحديث المشتريات بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseLine $purchaseLine)
    {
        $qty = $purchaseLine->quantity;
        $item = $purchaseLine->item;
        if ($item) {
            $item->decrement('stock', $qty);
            Stock::create([
                'item_id' => $item->id,
                'change' => -1 * $qty,
                'type' => 'purchase_delete',
                'reference_id' => $purchaseLine->id,
                'note' => 'Purchase line deleted',
            ]);
        }

        $purchase = $purchaseLine->purchase;

        // reverse Banking entries for this line
        if ($purchase) {
            $this->reversePurchaseBankingEntries($purchase, $purchaseLine->total, $purchaseLine->id);
        }

        $purchaseLine->delete();

        if ($purchase) {
            $purchase->total = $purchase->lines()->sum('total');
            $purchase->save();
        }

        return redirect()->back()->with('success', ' تم حذف الصنف بنجاح.');
    }

    protected function createPurchaseBankingEntries(Purchases $purchase, $amount, $referenceId = null)
    {
        $date = Carbon::now()->toDateString();

        // determine supplier Bank or fallback to Banks Payable
        $supplierBank = null;
        if ($purchase->supplier && $purchase->supplier->account_id) {
            $supplierBank = Bank::find($purchase->supplier->account_id);
        }

        if (! $supplierBank) {
            $supplierBank = Bank::firstOrCreate(
                ['name' => $purchase->supplier->name],
                ['type' => 'liability', 'number' => 'AP', 'total' => 0, 'kind' => 'payment']
            );
            $purchase->supplier->update([
                'account_id' => $supplierBank->id
            ]);
            $purchase->save();
        }

        // determine inventory or purchases Bank (debit)
        $inventoryBank = Bank::firstOrCreate(
            ['name' => 'ايردات المشتريات'],
            ['type' => 'asset', 'number' => 'Inventory', 'balance' => 0]
        );
        // Debit inventory (increase asset)
        $this->createTransactionAndAdjustBalance($inventoryBank, $amount, 'debit', 'فاتورة مشتريات ' . $purchase->reference_id, $date, $referenceId);

        // Credit supplier / AP
        $this->createTransactionAndAdjustBalance($supplierBank, $amount, 'credit', 'فاتورة مشتريات ' . $purchase->reference_id, $date, $referenceId);
    }

    protected function reversePurchaseBankingEntries(Purchases $purchase, $amount, $referenceId = null)
    {
        $date = Carbon::now()->toDateString();

        $supplierBank = null;
        if ($purchase->supplier && $purchase->supplier->account_id) {
            $supplierBank = Bank::find($purchase->supplier->account_id);
        }
        if (!$supplierBank) {
            $supplierBank = Bank::where('name', 'Banks Payable')->first();
        }

        $inventoryBank = Bank::where('name', 'Inventory')->first();

        if ($inventoryBank) {
            // Credit inventory to reverse (decrease asset)
            $this->createTransactionAndAdjustBalance($inventoryBank, $amount, 'credit', "Reversal Purchase #{$purchase->id} (line {$referenceId})", $date, $referenceId);
        }

        if ($supplierBank) {
            // Debit supplier/AP to reverse
            $this->createTransactionAndAdjustBalance($supplierBank, $amount, 'debit', "Reversal Purchase #{$purchase->id} (line {$referenceId})", $date, $referenceId);
        }
    }

    protected function createTransactionAndAdjustBalance(Bank $Bank, $amount, $side, $description = null, $date = null, $referenceId = null)
    {
        $date = $date ?? Carbon::now()->toDateString();

        Transaction::create([
            'account_id' => $Bank->id,
            'amount' => $amount,
            'type' => $side,
            'description' => $description,
            'date' => $date,
        ]);

        $debitIncrease = ['asset', 'bank', 'expense'];
        $creditIncrease = ['liability', 'equity', 'revenue', 'income'];

        if ($side === 'debit') {
            if (in_array($Bank->type, $debitIncrease)) {
                $Bank->balance = ($Bank->balance ?? 0) + $amount;
            } else {
                $Bank->balance = ($Bank->balance ?? 0) - $amount;
            }
        } else {
            if (in_array($Bank->type, $creditIncrease)) {
                $Bank->balance = ($Bank->balance ?? 0) + $amount;
            } else {
                $Bank->balance = ($Bank->balance ?? 0) - $amount;
            }
        }

        $Bank->save();
    }
}

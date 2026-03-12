<?php

namespace App\Http\Controllers;

use App\Models\PurchaseLine;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Bank;
use App\Models\Stock as ModelsStock;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseLineController extends Controller
{
    public function store(Request $request)
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
            $item->increment('stock', $data['quantity']);
            Stock::create([
                'item_id' => $item->id,
                'change' => $data['quantity'],
                'type' => 'مشتريات',
                'reference_id' => $line->id,
                'note' => '   تم شراء  '.$item->name,
            ]);
        }

        // update purchase total
        $purchase = Purchase::find($data['purchase_id']);
        if ($purchase) {
            $purchase->total = $purchase->lines()->sum('total');
            $purchase->save();

            // create Banking entries for the purchase line
            $this->createPurchaseBankingEntries($purchase, $line->total, $line->id);
        }

        return redirect()->back()->with('success', ' تم اضافة صنف بنجاح.');
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

    protected function createPurchaseBankingEntries(Purchase $purchase, $amount, $referenceId = null)
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
                ['type' => 'liability', 'number' => 'AP', 'total' => 0 , 'kind' => 'payment' ,'kind' => 'payment']
            );
            $purchase->supplier->update([
                'account_id'=> $supplierBank->id
            ]);
            $purchase->save();
        }

        // determine inventory or purchases Bank (debit)
        $inventoryBank = Bank::firstOrCreate(
            ['name' => 'Inventory'],
            ['type' => 'asset', 'number' => 'INV', 'balance' => 0]
        );

        // Debit inventory (increase asset)
        $this->createTransactionAndAdjustBalance($inventoryBank, $amount, 'debit', "Purchase #{$purchase->id} (line {$referenceId})", $date, $referenceId);

        // Credit supplier / AP
        $this->createTransactionAndAdjustBalance($supplierBank, $amount, 'credit', "Purchase #{$purchase->id} (line {$referenceId})", $date, $referenceId);
    }

    protected function reversePurchaseBankingEntries(Purchase $purchase, $amount, $referenceId = null)
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

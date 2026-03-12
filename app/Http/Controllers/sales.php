<?php

namespace App\Http\Controllers;

use App\Models\SaleLine;
use App\Models\Sales;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Bank;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesLineController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $data['total'] = $data['quantity'] * $data['unit_price'];

        $line = SaleLine::create($data);

        // Decrease item stock
        $item = Item::find($data['item_id']);
        if ($item) {
            $item->decrement('stock', $data['quantity']);
            Stock::create([
                'item_id' => $item->id,
                'change' => -1 * $data['quantity'],
                'type' => 'مبيعات',
                'reference_id' => $line->id,
                'note' => 'تم بيع ' . $item->name,
            ]);

            
        }

        // Update sale total
        $sale = Sales::find($data['sale_id']);
        if ($sale) {
            $sale->total = $sale->lines()->sum('total');
            $sale->save();

            $this->createSalesBankingEntries($sale, $line->total, $line->id);
        }

        return redirect()->back()->with('success', 'تمت إضافة صنف المبيعات.');
    }

    public function destroy(SaleLine $saleLine)
    {
        $qty = $saleLine->quantity;
        $item = $saleLine->item;
        if ($item) {
            $item->increment('stock', $qty);
            Stock::create([
                'item_id' => $item->id,
                'change' => $qty,
                'type' => 'sales_delete',
                'reference_id' => $saleLine->id,
                'note' => 'تم حذف صنف مبيعات',
            ]);
        }

        $sales = $saleLine->sales;

        if ($sales) {
            $this->reverseSalesBankingEntries($sales, $saleLine->total, $saleLine->id);
        }

        $saleLine->delete();

        if ($sales) {
            $sales->total = $sales->lines()->sum('total');
            $sales->save();
        }

        return redirect()->back()->with('success', 'تم حذف صنف المبيعات.');
    }

    protected function createSalesBankingEntries(Sales $sale, $amount, $referenceId = null)
    {
        $date = Carbon::now()->toDateString();

        // Customer account or fallback to "Accounts Receivable"
        $customerBank = null;
        if ($sale->customer && $sale->customer->account_id) {
            $customerBank = Bank::find($sale->customer->account_id);
        }

        if (! $customerBank) {
            $customerBank = Bank::firstOrCreate(
                ['name' => $sale->customer->name],
                ['type' => 'asset', 'number' => 'AR', 'balance' => 0]
            );
            $sale->customer->update(['account_id' => $customerBank->id]);
            $sale->save();
        }

        // Revenue account
        $revenueBank = Bank::firstOrCreate(
            ['name' => 'Sales Revenue'],
            ['type' => 'revenue', 'number' => 'REV', 'balance' => 0]
        );

        // Debit customer (increase asset)
        $this->createTransactionAndAdjustBalance($customerBank, $amount, 'debit', "Sale #{$sale->id} (line {$referenceId})", $date, $referenceId);

        // Credit revenue (increase income)
        $this->createTransactionAndAdjustBalance($revenueBank, $amount, 'credit', "Sale #{$sale->id} (line {$referenceId})", $date, $referenceId);
    }

    protected function reverseSalesBankingEntries(Sales $sale, $amount, $referenceId = null)
    {
        $date = Carbon::now()->toDateString();

        $customerBank = $sale->customer && $sale->customer->account_id
            ? Bank::find($sale->customer->account_id)
            : Bank::where('name', 'Accounts Receivable')->first();

        $revenueBank = Bank::where('name', 'Sales Revenue')->first();

        if ($revenueBank) {
            $this->createTransactionAndAdjustBalance($revenueBank, $amount, 'debit', "Reversal Sale #{$sale->id} (line {$referenceId})", $date, $referenceId);
        }

        if ($customerBank) {
            $this->createTransactionAndAdjustBalance($customerBank, $amount, 'credit', "Reversal Sale #{$sale->id} (line {$referenceId})", $date, $referenceId);
        }
    }

    protected function createTransactionAndAdjustBalance(Bank $bank, $amount, $side, $description = null, $date = null, $referenceId = null)
    {
        $date = $date ?? Carbon::now()->toDateString();

        Transaction::create([
            'account_id' => $bank->id,
            'amount' => $amount,
            'type' => $side,
            'description' => $description,
            'date' => $date,
        ]);

        $debitIncrease = ['asset', 'bank', 'expense'];
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
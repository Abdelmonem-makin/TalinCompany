<?php

namespace App\Http\Controllers;

use App\Models\SaleLine;
use App\Models\Sales;
use App\Models\Bank;
use App\Models\Item;
use App\Models\Stock;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SaleLineController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $item = Item::find($data['item_id']);
        if ($item->stock < $data['quantity']) {
            return redirect()->back()->withErrors(['الكمية المتاحة غير كافية للبيع.']);
        }

        $data['total'] = $data['quantity'] * $data['unit_price'];

        $line = SaleLine::create($data);

        // If the sale is already confirmed, adjust stock immediately; otherwise stock will be adjusted when accountant confirms the sale.
        $sale = Sales::find($data['sale_id']);
        if ($sale && $sale->status === 'confirmed') {
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
        }

        // update sale total (always reflect current lines)
        if ($sale) {
            $sale->total = $sale->lines()->sum('total');
            $sale->save();
            $this->createSalesBankingEntries($sale, $line->total, $line->id);
        }

        return redirect()->back()->with('success', 'Sale line added.');
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
    public function destroy(SaleLine $saleLine)
    {
        $qty = $saleLine->quantity;
        $item = $saleLine->item;
        // If sale already confirmed, restore stock immediately; otherwise no stock change.
        $sale = $saleLine->sale;
        if ($sale && $sale->status === 'confirmed' && $item) {
            $item->increment('stock', $qty);
            Stock::create([
                'item_id' => $item->id,
                'change' => $qty,
                'type' => 'sale_delete',
                'reference_id' => $saleLine->id,
                'note' => 'Sale line deleted (confirmed sale)',
            ]);
        }

        $saleLine->delete();

        if ($sale) {
            $sale->total = $sale->lines()->sum('total');
            $sale->save();
        }

        return redirect()->back()->with('success', 'Sale line removed.');
    }
}

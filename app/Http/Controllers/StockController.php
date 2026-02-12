<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Item;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $stocks = Stock::with('item')->when($search, function ($query) use ($search) {
            return $query->where('type', 'like', '%' . $search . '%')
                         ->orWhere('note', 'like', '%' . $search . '%')
                         ->orWhereHas('item', function ($q) use ($search) {
                             $q->where('name', 'like', '%' . $search . '%');
                         });
        })->latest()->paginate(10)->appends(['search' => $search]);
        $items = Item::pluck('name', 'id');
        $suppliers = \App\Models\Supplier::pluck('name', 'id');
        $has_pending_purchases = \App\Models\Purchases::where('status', '!=', 'received')->exists();

        // Fetch expired stocks for notifications
        $expiredStocks = Stock::with('item')
            ->where('is_expired', true)
            ->where('quantity', '>', 0)
            ->get();

        // Fetch stocks expiring soon (within 7 days)
        $expiringSoonStocks = Stock::with('item')
            ->where('expiry', '>', now())
            ->where('expiry', '<=', now()->addDays(7))
            ->where('quantity', '>', 0)
            ->get();

        return view('stock.index', compact('stocks', 'items', 'suppliers', 'has_pending_purchases', 'expiredStocks', 'expiringSoonStocks', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::pluck('name', 'id');
        return view('stock.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock' => 'required|numeric',
            'item_id' => 'required|exists:items,id',
            'change' => 'required|numeric',
            'type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'note' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'customer_id' => 'nullable|exists:customers,id',
            'sign' => 'nullable|in:-1,1',
            'expiry' => 'required|date',
        ]);

        // apply sign if provided (issue -> negative)
        if (isset($validated['sign']) && $validated['sign'] == -1) {
            $validated['change'] = -abs($validated['change']);
        }

        // attach supplier/customer info into note (lightweight)
        $itemst = Item::find($validated['item_id']);

        $meta = [];
        if (!empty($validated['supplier_id'])) {
            $supplier = \App\Models\Supplier::findOrFail($validated['supplier_id']);

            $meta[] = 'تم استلام مشتريات من المورد :- ' . $supplier->name . '    عدد ' . abs($validated['change']) . ' من الصنف ' . $itemst->name;
        }

        if (!empty($validated['customer_id'])) {
            $customer = \App\Models\Customer::findOrFail($validated['customer_id']);

            $meta[] = 'تم صرف مبيعات الى عميل :- ' . $customer->name . '    عدد ' . abs($validated['change']) . ' من الصنف ' . $itemst->name;
        }
        if ( !empty($validated['customer_id']) == 'defaultCustomer') {
            $meta[] = 'تم صرف مبيعات الى عميل :-   افتراضي     عدد ' . abs($validated['change']) . ' من الصنف ' . $itemst->name;
        }
        if (!empty($meta)) {
            $validated['note'] = trim(($validated['note'] ?? '') . ' | ' . implode(' | ', $meta), " | ");
        }
        $stockid = Stock::find($validated['stock']);
        $stocks = $stockid->remaining + $validated['change'];
        if ($stockid->quantity < $stocks) {
            return redirect()->back()->withErrors(['      الكمية المدخلة تتجاوز الكمية المشتراه في هذا السجل.']);
        }

        // build data for Stock (only fillable fields)

        $stockData = [
            // 'purchase_id' => $validated['reference_id'],
            'item_id' => $validated['item_id'],
            'quantity' => $validated['change'],
            'type' => !empty($validated['customer_id']) ? 'مبيعات' : 'مشتريات',
            'reference_id' => $validated['reference_id'] ?? null,
            'note' => ($validated['note'] ?? ''),
            'status' => 'confirm',
            'expiry' => $validated['expiry'] ?? null,
        ];

        Stock::create($stockData);
        $stockid->increment('remaining', $validated['change']);
        // dd();
        if ($stockid->quantity == $stockid->remaining) {
            $stockid->update(['status' => 'confirm']);
        }
        // also update item stock
        $item = Item::find($stockData['item_id']);
        if ($item) {
            $item->increment('stock', $stockData['quantity']);
        }

        return redirect()->route('stock.index')->with('success', 'تم تحديث المخزون بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stock = Stock::with('item')->findOrFail($id);
        return view('stock.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $stock = Stock::findOrFail($id);
        $items = Item::pluck('name', 'id');
        return view('stock.edit', compact('stock', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $stock = Stock::findOrFail($id);
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric',
            'type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'note' => 'nullable|string',
        ]);

        // adjust item stock by difference
        $oldChange = $stock->change;
        $diff = $data['change'] - $oldChange;

        $stock->update($data);

        $item = Item::find($data['item_id']);
        if ($item && $diff != 0) {
            $item->increment('stock', $diff);
        }

        return redirect()->route('stock.show', $stock)->with('success', 'Stock entry updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stock = Stock::findOrFail($id);
        $item = $stock->item;
        if ($item) {
            $item->decrement('stock', $stock->change);
        }
        $stock->delete();
        return redirect()->route('stock.index')->with('success', 'Stock entry deleted.');
    }

    /**
     * Dispose of expired products.
     */
    public function dispose(Request $request)
    {
        $disposals = $request->input('disposals', []);

        $disposedCount = 0;
        $totalDisposedQuantity = 0;

        foreach ($disposals as $stockId => $quantity) {
            $quantity = (int) $quantity;

            if ($quantity <= 0) {
                continue;
            }


            $stock = Stock::find($stockId);

            // dd($stock);
            if (!$stock || !$stock->is_expired || $stock->quantity < $quantity) {
                continue;
            }

            // Create a disposal stock entry (negative quantity)
            Stock::create([
                'item_id' => $stock->item_id,
                'quantity' => -$quantity,
                'type' => 'تخلص',
                'note' => 'تم التخلص من منتجات منتهية الصلاحية - كمية: ' . $quantity . ' من ' . $stock->item->name,
                'status' => 'dispose',
                'expiry' => $stock->expiry,
            ]);

            // Update the original stock entry
            // $stock->decrement('remaining', $quantity);
            if ($stock->status !=  'dispose') {
                $stock->is_expired = false;
                $stock->status = 'dispose';
                $stock->save();
            }
            // Update item stock
            $item = $stock->item;
            if ($item && $item->stock >= $quantity) {
                $item->decrement('stock', $quantity);
            }

            $disposedCount++;
            $totalDisposedQuantity += $quantity;
        }

        if ($disposedCount > 0) {
            return redirect()->route('stock.index')->with(
                'success',
                "تم التخلص من {$totalDisposedQuantity} قطعة من {$disposedCount} منتج منتهي الصلاحية بنجاح."
            );
        }

        return redirect()->back()->withErrors('لم يتم التخلص من أي منتجات.');
    }
}

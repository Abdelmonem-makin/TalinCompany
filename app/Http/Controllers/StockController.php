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
    public function index()
    {
        $stocks = Stock::with('item')->latest()->paginate(25);
        $items = Item::pluck('name', 'id');
        $suppliers = \App\Models\Supplier::pluck('name', 'id');
        return view('stock.index', compact('stocks', 'items', 'suppliers'));
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
            'item_id' => 'required|exists:items,id',
            'change' => 'required|numeric',
            'type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'note' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'customer_id' => 'nullable|exists:customers,id',
            'sign' => 'nullable|in:-1,1',
            'expiry' => 'nullable|date',
        ]);

        // apply sign if provided (issue -> negative)
        if (isset($validated['sign']) && $validated['sign'] == -1) {
            $validated['change'] = -abs($validated['change']);
        }

        // attach supplier/customer info into note (lightweight)

        $meta = [];
        if (!empty($validated['supplier_id'])) {
        $supplier = \App\Models\Supplier::findOrFail($validated['supplier_id']);

            $meta[] = 'استلام مشتريات من المورد :- ' . $supplier->name ;
        }

        if (!empty($validated['customer_id'])) {
        $customer = \App\Models\Customer::findOrFail($validated['customer_id']);

            $meta[] = 'صرف مبيعات الى عميل :- ' . $customer->name;
        }
        if (!empty($meta)) {
            $validated['note'] = trim(($validated['note'] ?? '') . ' | ' . implode(' | ', $meta), " | ");
        }

        // build data for Stock (only fillable fields)
        $stockData = [

            'item_id' => $validated['item_id'],
            'change' => $validated['change'],
             'type' => !empty($validated['customer_id'] ) ? 'مبيعات': 'مشتريات' ,
            'reference_id' => $validated['reference_id'] ?? null,
            'note' => $validated['note'] ?? null,
            'expiry' => $validated['expiry'] ?? null,   
        ];

        $stock = Stock::create($stockData);
        
        // also update item stock
        $item = Item::find($stockData['item_id']);
        if ($item) {
            $item->increment('stock', $stockData['change']);
        }

        return redirect()->route('stock.index')->with('success', 'Stock entry created.');
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
            'change' => 'required|numeric',
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
}

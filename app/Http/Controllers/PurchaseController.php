<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier')->latest()->paginate(15);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::pluck('name', 'id');
        return view('purchases.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            // 'date' => 'nullable|date',
            'total' => 'nullable|numeric',
            // 'status' => 'nullable|string',
        ]);

        Purchase::create($data);
        return redirect()->route('purchases.index')->with('success', 'تم الاضافه بنجاح .');
    }

    public function show(Purchase $purchase)
    {
        $items = \App\Models\Item::pluck('name', 'id');
        return view('purchases.show', compact('purchase', 'items'));
    }

    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::pluck('name', 'id');
        return view('purchases.edit', compact('purchase', 'suppliers'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            // 'date' => 'nullable|date',
            'total' => 'nullable|numeric',
            // 'status' => 'nullable|string',
        ]);

        $purchase->update($data);
        return redirect()->route('purchases.index')->with('success', 'تم التحديث بنجاح .');
    }

    public function destroy(Purchase $purchase)
    {
        // optionally reduce stock based on lines
        foreach ($purchase->lines as $line) {
            $item = $line->item;
            if ($item) {
                $item->decrement('stock', $line->quantity);
            }
        }

        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', ' تم الحذف  .');
    }
}

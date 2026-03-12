<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('supplier')->latest()->paginate(15);
        $suppliers = Supplier::pluck('name', 'id');

        return view('items.index', compact('items','suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::pluck('name', 'id');
        return view('items.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            // 'sku' => 'nullable|string|max:100',
            // 'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            // 'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        Item::create($data);
        return redirect()->route('items.index')->with('success', ' تم اضافة الصنف بنجاح .');
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $suppliers = Supplier::pluck('name', 'id');
        return view('items.edit', compact('item', 'suppliers'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'company' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            // 'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $item->update($data);
        return redirect()->route('items.index')->with('success', ' تم تحديث بيانات الصنف بنجاح  .');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'تم حذف الصنف بنجاح  .');
    }
}

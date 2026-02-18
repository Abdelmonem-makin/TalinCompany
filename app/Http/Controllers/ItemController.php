<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ItemController extends Controller
{
            public function __construct()
    {
                $this->middleware('auth');
        $this->middleware(['permission:items_read'])->only('index');
        $this->middleware(['permission:items_read'])->only('show');
        $this->middleware(['permission:items_create'])->only('create');
        $this->middleware(['permission:items_create'])->only('store');
        $this->middleware(['permission:items_update'])->only('edit');
        $this->middleware(['permission:items_update'])->only('update');
        $this->middleware(['permission:items_delete'])->only('destroy');
    }
    public function index(Request $request)
    {
        $search = $request->get('search');
        $items = Item::with('supplier')->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                         ->orWhere('type', 'like', '%' . $search . '%')
                         ->orWhere('company', 'like', '%' . $search . '%');
        })->latest()->paginate(15)->appends(['search' => $search]);
        $suppliers = Supplier::pluck('name', 'id');

        return view('items.index', compact('items', 'suppliers', 'search'));
    }

    public function create()
    {
        $suppliers = Supplier::pluck('name', 'id');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'company' => 'nullable|string',
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
        // return view('items.edit', compact('item', 'suppliers'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
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

    public function getItemsData()
    {
        $items = Item::latest()->get(['id', 'name','type', 'stock', 'price']);
        return response()->json($items);
    }

    public function getStock(Item $item)
    {
        return response()->json(['stock' => $item->stock]);
    }
}

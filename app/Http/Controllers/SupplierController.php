<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{ 
        public function __construct()
    {
                $this->middleware('auth');
        $this->middleware(['permission:suppliers_read'])->only('index');
        $this->middleware(['permission:suppliers_read'])->only('show');
        $this->middleware(['permission:suppliers_create'])->only('create');
        $this->middleware(['permission:suppliers_create'])->only('store');
        $this->middleware(['permission:suppliers_update'])->only('edit');
        $this->middleware(['permission:suppliers_update'])->only('update');
        $this->middleware(['permission:suppliers_delete'])->only('destroy');
    }
    public function index(Request $request)
    {
        $search = $request->get('search');
        $suppliers = Supplier::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                         ->orWhere('email', 'like', '%' . $search . '%')
                         ->orWhere('phone', 'like', '%' . $search . '%');
        })->latest()->paginate(15)->appends(['search' => $search]);
        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create()
    {
       
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier created.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
 
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'account_id' => 'nullable|exists:banks,id',
        ]);

        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }
}

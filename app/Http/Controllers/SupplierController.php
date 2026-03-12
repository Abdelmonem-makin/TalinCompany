<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $accounts = \App\Models\Account::pluck('name', 'id');
        return view('suppliers.create', compact('accounts'));
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
        $accounts = \App\Models\bank::pluck('name', 'id');
        return view('suppliers.edit', compact('supplier', 'accounts'));
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

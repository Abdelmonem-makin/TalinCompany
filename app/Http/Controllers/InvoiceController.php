<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::pluck('name', 'id');
        return view('invoices.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'total' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);

        Invoice::create($data);
        return redirect()->route('invoices.index')->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $customers = Customer::pluck('name', 'id');
        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'total' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);

        $invoice->update($data);
        return redirect()->route('invoices.index')->with('success', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}

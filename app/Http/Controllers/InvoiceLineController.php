<?php

namespace App\Http\Controllers;

use App\Models\InvoiceLine;
use App\Models\Invoice;
use App\Models\Item;
use Illuminate\Http\Request;

class InvoiceLineController extends Controller
{
    public function index()
    {
        $lines = InvoiceLine::with(['invoice', 'item'])->latest()->paginate(20);
        return view('invoice_lines.index', compact('lines'));
    }

    public function create()
    {
        $invoices = Invoice::pluck('id', 'id');
        $items = Item::pluck('name', 'id');
        return view('invoice_lines.create', compact('invoices', 'items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'total' => 'nullable|numeric',
        ]);

        if (empty($data['total'])) {
            $data['total'] = $data['quantity'] * $data['unit_price'];
        }

        InvoiceLine::create($data);
        return redirect()->route('invoice-lines.index')->with('success', 'Invoice line created.');
    }

    public function show(InvoiceLine $invoiceLine)
    {
        return view('invoice_lines.show', ['line' => $invoiceLine]);
    }

    public function edit(InvoiceLine $invoiceLine)
    {
        $invoices = Invoice::pluck('id', 'id');
        $items = Item::pluck('name', 'id');
        return view('invoice_lines.edit', compact('invoiceLine', 'invoices', 'items'));
    }

    public function update(Request $request, InvoiceLine $invoiceLine)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'total' => 'nullable|numeric',
        ]);

        if (empty($data['total'])) {
            $data['total'] = $data['quantity'] * $data['unit_price'];
        }

        $invoiceLine->update($data);
        return redirect()->route('invoice-lines.index')->with('success', 'Invoice line updated.');
    }

    public function destroy(InvoiceLine $invoiceLine)
    {
        $invoiceLine->delete();
        return redirect()->route('invoice-lines.index')->with('success', 'Invoice line deleted.');
    }
}

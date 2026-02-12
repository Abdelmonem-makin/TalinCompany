<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $customers = Customer::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                         ->orWhere('phone', 'like', '%' . $search . '%');
        })->latest()->paginate(15)->appends(['search' => $search]);
        // $accounts = \App\Models\Account::pluck('name', 'id');
        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        $accounts = \App\Models\Account::pluck('name', 'id');
        return view('customers.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', '   تم اضافة عميل بنجاح.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $accounts = \App\Models\Account::pluck('name', 'id');
        return view('customers.edit', compact('customer', 'accounts'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', '   تم تحديث بيانات العميل بنجاح.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'تم حذف العميل بنجاح  .');
    }
}

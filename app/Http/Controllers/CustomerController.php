<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Customer $customer)
    {
        $customers = Customer::latest()->paginate(15);
        // $accounts = \App\Models\Account::pluck('name', 'id');
        return view('customers.index', compact('customers' , 'customer'));
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

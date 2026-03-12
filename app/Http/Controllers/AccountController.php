<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Bank::paginate(15);
        return view('accounts.index', compact('accounts'));
    }

    public function debts()
    {
        // Customers with linked account balances (accounts receivable)
        $customers = \App\Models\Customer::with('bank')
            ->get()
            ->map(function($c){
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'balance' => optional($c->bank)->balance ?? 0,
                ];
            });

        // Suppliers with linked account balances (accounts payable)
        $suppliers = \App\Models\Supplier::with('bank')
            ->get()
            ->map(function($s){
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'balance' => optional($s->bank)->balance ?? 0,
                ];
            });

        return view('accounts.debts', compact('customers', 'suppliers'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:50',
            'balance' => 'nullable|numeric',
        ]);

        Bank::create($data);
        return redirect()->route('accounts.index')->with('success', ' تم انشاء الحساب بنجاح.');
    }

    public function show(bank $account)
    {
        // load receipts/payments related to this account using `kind` for reliability
        $transactions = $account->transactions()
            ->with('bank')
            ->orderBy('date', 'desc')
            ->paginate(25);

        return view('accounts.show', compact('account', 'transactions'));
    }

    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:50',
            'balance' => 'nullable|numeric',
        ]);

        $account->update($data);
        return redirect()->route('accounts.index')->with('success', 'تم تحديث بيانات الحساب بنجاح  .');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', '  تم جحذف الحساب بنجاح.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Bank::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('number', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%');
        }

        $accounts = $query->paginate(8);
        $Banks = Bank::where('type', 'CASH')->get();
        $customers = Customer::get();
        $suppliers = Supplier::get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('accounts.partials.table', compact('accounts', 'Banks', 'customers', 'suppliers'))->render(),
                'pagination' => $accounts->links()->toHtml()
            ]);
        }

        return view('accounts.index', compact('accounts', 'Banks', 'suppliers', 'customers'));
    }

    public function debts()
    {
        // Customers with linked account balances (accounts receivable)
        $customers = \App\Models\Customer::with('bank')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'balance' => optional($c->bank)->balance ?? 0,
                ];
            });

        // Suppliers with linked account balances (accounts payable)
        $suppliers = \App\Models\Supplier::with('bank')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'balance' => optional($s->bank)->balance ?? 0,
                ];
            });

        return view('accounts.debts', compact('customers', 'suppliers'));
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
            // ->orderBy('date')
            ->latest()
            ->paginate(25);

        return view('accounts.show', compact('account', 'transactions'));
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

    public function destroy(Bank $account)
    {
        // dd($account);
        if($account->balance == 0){

            foreach ($account->transactions as $transaction) {
                $transaction->delete();
            }
            foreach ($account->Expense as $Expense) {
                $Expense->delete();
            }
            if ($account->suppliers) {
                $account->suppliers->update(['account_id' => null]);
            }
            if ($account->customers) {
                $account->customers->update(['account_id' => null]);
            }
            $account->delete();
            return redirect()->back()->with('success', 'تم حذف الحساب بنجاح.');

        } else {
            return redirect()->back()->withErrors([
                'amount' => 'هذا الحساب عليه التزام مالي الرجاء الدفع أو السداد أولاً.',
            ]);
        }
    }
}

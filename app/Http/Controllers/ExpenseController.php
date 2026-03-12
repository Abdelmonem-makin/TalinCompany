<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\bank;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('account')->latest()->paginate(20);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $accounts = bank::pluck('name', 'id');
        return view('expenses.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'nullable|exists:banks,id',
            'amount' => 'required|numeric',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $expense = Expense::create($data);

        // accounting entries: Debit expense account, Credit payment account (account_id)
        $this->createExpenseAccountingEntries($expense);

        return redirect()->route('expenses.index')->with('success', 'Expense created.');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $accounts = bank::pluck('name', 'id');
        return view('expenses.edit', compact('expense', 'accounts'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'account_id' => 'nullable|exists:accounts,id',
            'amount' => 'required|numeric',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        // Optionally reverse previous accounting entries (not implemented granularly)
        $expense->update($data);
        // Create new accounting entries for updated expense
        $this->createExpenseAccountingEntries($expense);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        // create reversing entries
        $this->reverseExpenseAccountingEntries($expense);

        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }

    protected function createExpenseAccountingEntries(Expense $expense)
    {
        $date = $expense->date ? Carbon::parse($expense->date)->toDateString() : Carbon::now()->toDateString();

        // expense account (by category) or generic 'Expenses'
        $expenseAccountName = $expense->category ?: 'Expenses';
        $expenseAccount = bank::firstOrCreate(
            ['name' => $expenseAccountName],
            ['type' => 'expense', 'number' => strtoupper(substr($expenseAccountName,0,4)), 'balance' => 0]
        );

        // payment account (the account from which expense was paid) or 'Cash'
        $paymentAccount = null;
        if ($expense->account_id) {
            $paymentAccount = bank::find($expense->account_id);
        }
        if (! $paymentAccount) {
            $paymentAccount = bank::firstOrCreate(
                ['name' => 'Cash'],
                ['type' => 'bank', 'number' => 'CASH', 'balance' => 0]
            );
        }

        // Debit expense
        Transaction::create([
            'account_id' => $expenseAccount->id,
            'amount' => $expense->amount,
            'type' => 'debit',
            'description' => $expense->description ?? "Expense #{$expense->id}",
            'date' => $date,
        ]);
        // adjust balance
        $expenseAccount->balance = ($expenseAccount->balance ?? 0) + $expense->amount;
        $expenseAccount->save();

        // Credit payment account
        Transaction::create([
            'account_id' => $paymentAccount->id,
            'amount' => $expense->amount,
            'type' => 'credit',
            'description' => $expense->description ?? "Expense #{$expense->id}",
            'date' => $date,
        ]);
        // adjust balance for payment account
        $creditIncrease = ['liability', 'equity', 'revenue', 'income'];
        if (in_array($paymentAccount->type, $creditIncrease)) {
            $paymentAccount->balance = ($paymentAccount->balance ?? 0) + $expense->amount;
        } else {
            $paymentAccount->balance = ($paymentAccount->balance ?? 0) - $expense->amount;
        }
        $paymentAccount->save();
    }

    protected function reverseExpenseAccountingEntries(Expense $expense)
    {
        $date = $expense->date ? Carbon::parse($expense->date)->toDateString() : Carbon::now()->toDateString();

        $expenseAccountName = $expense->category ?: 'Expenses';
        $expenseAccount = bank::where('name', $expenseAccountName)->first();

        $paymentAccount = $expense->account_id ? bank::find($expense->account_id) : bank::where('name','Cash')->first();

        if ($expenseAccount) {
            Transaction::create([
                'account_id' => $expenseAccount->id,
                'amount' => $expense->amount,
                'type' => 'credit',
                'description' => "Reversal Expense #{$expense->id}",
                'date' => $date,
            ]);
            $expenseAccount->balance = ($expenseAccount->balance ?? 0) - $expense->amount;
            $expenseAccount->save();
        }

        if ($paymentAccount) {
            Transaction::create([
                'account_id' => $paymentAccount->id,
                'amount' => $expense->amount,
                'type' => 'debit',
                'description' => "Reversal Expense #{$expense->id}",
                'date' => $date,
            ]);
            $creditIncrease = ['liability', 'equity', 'revenue', 'income'];
            if (in_array($paymentAccount->type, $creditIncrease)) {
                $paymentAccount->balance = ($paymentAccount->balance ?? 0) - $expense->amount;
            } else {
                $paymentAccount->balance = ($paymentAccount->balance ?? 0) + $expense->amount;
            }
            $paymentAccount->save();
        }
    }
}

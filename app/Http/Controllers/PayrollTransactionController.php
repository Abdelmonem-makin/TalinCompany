<?php

namespace App\Http\Controllers;

use App\Models\PayrollTransaction;
use App\Models\Employee;
use App\Models\Bank;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollTransactionController extends Controller
{
    public function index()
    {
        $rows = PayrollTransaction::with('employee')->latest()->paginate(20);
        return view('payroll_transactions.index', compact('rows'));
    }

    public function create()
    {
        $employees = Employee::pluck('name', 'id');
        return view('payroll_transactions.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);
        $pt = PayrollTransaction::create($data);
        // create Banking entries: Debit Salaries Expense, Credit Cash/Bank
        $this->createPayrollBankingEntries($pt);

        return redirect()->route('employees.index')->with('success', 'Payroll transaction created.');
    }

    public function show(PayrollTransaction $payrollTransaction)
    {
        return view('payroll_transactions.show', compact('payrollTransaction'));
    }

    public function edit(PayrollTransaction $payrollTransaction)
    {
        $employees = Employee::pluck('name', 'id');
        return view('payroll_transactions.edit', compact('payrollTransaction', 'employees'));
    }

    public function update(Request $request, PayrollTransaction $payrollTransaction)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $payrollTransaction->update($data);
        // create Banking entries for updated payroll (simple approach)
        $this->createPayrollBankingEntries($payrollTransaction);

        return redirect()->route('employees.index')->with('success', 'Payroll transaction updated.');
    }

    public function destroy(PayrollTransaction $payrollTransaction)
    {
        // reverse Banking entries
        $this->reversePayrollBankingEntries($payrollTransaction);

        $payrollTransaction->delete();
        return redirect()->route('payroll-transactions.index')->with('success', 'Payroll transaction deleted.');
    }

    protected function createPayrollBankingEntries(PayrollTransaction $pt)
    {
        $date = $pt->date ? Carbon::parse($pt->date)->toDateString() : Carbon::now()->toDateString();

        // Salaries expense Bank
        $salaryBank = Bank::firstOrCreate(
            ['name' => 'مرتبات'],
            ['type' => 'expense', 'number' => 'SAL', 'balance' => 0]
        );

        // Payment Bank (Cash) by default
        $paymentBank = Bank::firstOrCreate(
            ['name' => 'الخزينه'],
            ['type' => 'CASH', 'number' => 'CASH-01', 'balance' => 0]
        );
     if ($paymentBank->balance <  $pt->amount) {
            return redirect()->back()->withErrors([
                'amount' => 'المبلغ المدخل أكبر من الرصيد المتاح في الخزينة.'
            ]);
        }else{
        // Debit salaries expense
        Transaction::create([
            'account_id' => $salaryBank->id,
            'amount' => $pt->amount,
            'type' => 'debit',
            'description' => $pt->description ?? " مرتب  -  {$pt->employee->name}",
            'date' => $date,
        ]);
        $salaryBank->balance = ($salaryBank->balance ?? 0) + $pt->amount;
        $salaryBank->save();

        // Credit payment Bank
        Transaction::create([
            'account_id' => $paymentBank->id,
            'amount' => $pt->amount,
            'type' => 'credit',
            'description' => $pt->description ?? "مرتب  - {$pt->employee->name}",
            'date' => $date,
        ]);
        $creditIncrease = ['liability', 'equity', 'revenue', 'income'];
        if (in_array($paymentBank->type, $creditIncrease)) {
            $paymentBank->balance = ($paymentBank->balance ?? 0) + $pt->amount;
        } else {
            $paymentBank->balance = ($paymentBank->balance ?? 0) - $pt->amount;
        }
        $paymentBank->save();

    }
    }

    protected function reversePayrollBankingEntries(PayrollTransaction $pt)
    {
        $date = $pt->date ? Carbon::parse($pt->date)->toDateString() : Carbon::now()->toDateString();

        $salaryBank = Bank::where('name', 'Salaries Expense')->first();
        $paymentBank = Bank::where('name', 'Cash')->first();

        if ($salaryBank) {
            Transaction::create([
                'account_id' => $salaryBank->id,
                'amount' => $pt->amount,
                'type' => 'credit',
                'description' => "Reversal Payroll #{$pt->id}",
                'date' => $date,
            ]);
            $salaryBank->balance = ($salaryBank->balance ?? 0) - $pt->amount;
            $salaryBank->save();
        }

        if ($paymentBank) {
            Transaction::create([
                'account_id' => $paymentBank->id,
                'amount' => $pt->amount,
                'type' => 'debit',
                'description' => "Reversal Payroll #{$pt->id}",
                'date' => $date,
            ]);
            $creditIncrease = ['liability', 'equity', 'revenue', 'income'];
            if (in_array($paymentBank->type, $creditIncrease)) {
                $paymentBank->balance = ($paymentBank->balance ?? 0) - $pt->amount;
            } else {
                $paymentBank->balance = ($paymentBank->balance ?? 0) + $pt->amount;
            }
            $paymentBank->save();
        }
    }
}

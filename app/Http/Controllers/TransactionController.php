<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('bank')->latest()->paginate(20);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = Bank::pluck('name', 'id');
        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:banks,id',
            'amount' => 'required|numeric',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        Transaction::create($data);
        return redirect()->route('transactions.index')->with('success', 'Transaction created.');
    }

    public function show(Transaction $transaction)
    {
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $accounts = Bank::pluck('name', 'id');
        return view('transactions.edit', compact('transaction', 'accounts'));
    }

    public function createReceipt()
    {
        $accounts = Bank::pluck('name', 'id');
        $customers = Customer::pluck('name', 'id');
        return view('transactions.receipt', compact('accounts', 'customers'));
    }

    public function storeReceipt(Request $request)
    {
        $data = $request->validate([
            'account_id' => 'nullable|exists:banks,id', // payment Bank (bank/cash) optional: will use default cash if missing
            'amount' => 'required|numeric|min:0.01',
            'bank' => 'nullable|numeric|min:0.01',
            'customer_id' => 'nullable|exists:customers,id',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        $date = $data['date'] ?? Carbon::now()->toDateString();

        // payment Bank (cash/bank) - Debit
        if (!empty($data['account_id'])) {
            $paymentBank = Bank::find($data['account_id']);
        } else {
            // default to a cash/bank account named 'الصندوق'
            $paymentBank = Bank::firstOrCreate(['name' => 'الخزينه'], ['type' => 'CASH', 'number' => 'CASH-01', 'balance' => 0 ,'kind' => 'payment']);
        }
        if (!empty($data['bank']) && $data['bank'] > 0) {
            $bankAccount =   Bank::firstOrCreate(
                ['name' => 'حساب بنكي'],
                ['type' => 'Bank', 'number' => 'Bank-01', 'balance' => 0 ,'kind' => 'payment'
]
            );
            // $data['amount'] = $data['amount'] - $data['bank'];
            if ($bankAccount) {
                $this->adjustBankBalance($bankAccount, $data['bank'], 'debit',);
            }
        }
        // customer Bank (AR) or create a dedicated AR account for the customer
        $customerBank = null;
        if (!empty($data['customer_id'])) {
            $customer = Customer::find($data['customer_id']);
            if ($customer) {
                if ($customer->account_id) {
                    $customerBank = Bank::find($customer->account_id);
                }
            }
        } else {
            // fallback to shared AR account
            $customerBank = Bank::firstOrCreate(['name' =>  'عميل افتراضي'], ['type' => 'asset', 'number' => 'CU-ACCUNT-01', 'balance' => 0 , 'kind' => 'receipt']);
        }
        // التحقق من حاسب العميل اذا كان عليه ديون
        if ($customerBank->balance < $data['amount']) {
            return redirect()->back()->withErrors([
                'amount' => 'المبلغ المدخل أكبر من الرصيد المطلوب من العميل.'
            ]);
        }
        // Debit payment Bank (increase asset)
        Transaction::create(['account_id' => $paymentBank->id, 'amount' => $data['amount'], 'type' => 'debit', 'description' => $data['description'] ?? "قبض", 'date' => $date, 'kind' => 'receipt']);
        $this->adjustBankBalance($paymentBank, $data['amount'], 'debit');
        $data['amount'] = $data['amount'] + $data['bank'];
        // Credit customer AR (decrease AR)
        Transaction::create(['account_id' => $customerBank->id, 'amount' => $data['amount'], 'type' => 'credit', 'description' => $data['description'] ?? "قبض", 'date' => $date, 'kind' => 'receipt']);
        $this->adjustBankBalance($customerBank, $data['amount'], 'credit');

        return redirect()->route('accounts.index')->with('success', ' تم القبض بنجاح .');
    }

    public function createPayment()
    {
        $accounts = Bank::pluck('name', 'id');
        $suppliers = Supplier::pluck('name', 'id');
        return view('transactions.payment', compact('accounts', 'suppliers'));
    }

    public function storePayment(Request $request,)
    {
        $data = $request->validate([
            'account_id' => 'exists:banks,id', // payment Bank (bank/cash)
            'amount' => 'required|numeric|min:0.01',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        $date = $data['date'] ?? Carbon::now()->toDateString();

        $paymentBank = Bank::find($data['account_id']);
        // ✅ تحقق من الرصيد قبل الدفع
        if ($paymentBank->balance < $data['amount']) {
            return redirect()->back()->withErrors([
                'amount' => 'المبلغ المدخل أكبر من الرصيد المتاح في الخزينة.'
            ]);
        }
        // supplier Bank (AP)
        $supplierBank = null;
        if (!empty($data['supplier_id'])) {
            $supplier = Supplier::find($data['supplier_id']);
            if ($supplier && $supplier->account_id) {
                $supplierBank = Bank::find($supplier->account_id);
            }
        } else {
            $supplierBank = Bank::firstOrCreate(['name' =>  'حساب موردين'], ['type' => 'liability', 'number' => 'AP', 'balance' => 0 ,'kind' => 'payment']);
        }

        // Debit supplier/AP to reduce liability
        Transaction::create(['account_id' => $supplierBank->id, 'amount' => $data['amount'], 'type' => 'debit', 'description' => $data['description'] ?? 'دفع', 'date' => $date, 'kind' => 'payment']);
        $this->adjustBankBalance($supplierBank, $data['amount'], 'debit');

        // Credit payment Bank (cash out)
        Transaction::create(['account_id' => $paymentBank->id, 'amount' => $data['amount'], 'type' => 'credit', 'description' => $data['description'] ?? 'دفع', 'date' => $date, 'kind' => 'payment']);
        $this->adjustBankBalance($paymentBank, $data['amount'], 'credit');

        return redirect()->route('transactions.index')->with('success', 'تم الدفع بنجاح  .');
    }

    protected function adjustBankBalance(Bank $Bank, $amount, $side)
    {
        $debitIncrease = ['asset', 'CASH', 'Bank', 'expense'];
        $creditIncrease = ['liability', 'equity', 'revenue', 'income'];

        if ($side === 'debit') {
            if (in_array($Bank->type, $debitIncrease)) {
                $Bank->balance = ($Bank->balance ?? 0) + $amount;
            } else {
                $Bank->balance = ($Bank->balance ?? 0) - $amount;
            }
        } else {
            if (in_array($Bank->type, $creditIncrease)) {
                $Bank->balance = ($Bank->balance ?? 0) + $amount;
            } else {
                $Bank->balance = ($Bank->balance ?? 0) - $amount;
            }
        }

        $Bank->save();
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:banks,id',
            'amount' => 'required|numeric',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        $transaction->update($data);
        return redirect()->route('transactions.index')->with('success', 'Transaction updated.');
    }

    public function destroy(Transaction $transaction)
    {
        // Instead of physical delete, mark as reversed (void) and note reason.
        $transaction->reversed = true;
        $transaction->reversal_note = 'منع الحذف: تم وضع العلامة كملغى';
        $transaction->save();
        return redirect()->route('transactions.index')->with('success', 'Transaction marked as reversed (voided).');
    }
}

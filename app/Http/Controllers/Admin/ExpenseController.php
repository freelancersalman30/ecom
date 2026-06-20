<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    // ✅ List + Summary
    public function index()
    {
        // ফান্ড ব্যালেন্স
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        $balance   = $total_in - $total_out;

        $today        = Carbon::today();
        $currentYear  = $today->year;
        $currentMonth = $today->month;

        // এই বছরে মোট খরচ
        $yearlyExpense = Expense::whereYear('expense_date', $currentYear)->sum('amount');

        // এই মাসে মোট খরচ
        $monthlyExpense = Expense::whereYear('expense_date', $currentYear)
                            ->whereMonth('expense_date', $currentMonth)
                            ->sum('amount');

        // আজকের খরচ
        $todayExpense = Expense::whereDate('expense_date', $today)->sum('amount');

        // হিস্টরি
        $expenses = Expense::orderBy('expense_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(20);

        return view('backEnd.expenses.index', compact(
            'balance',
            'currentYear',
            'currentMonth',
            'yearlyExpense',
            'monthlyExpense',
            'todayExpense',
            'expenses'
        ));
    }

    // ✅ Store Expense
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category'     => 'nullable|string|max:100',
            'note'         => 'nullable|string',
        ]);

        // ব্যালেন্স চেক
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        $balance   = $total_in - $total_out;

        if ($validated['amount'] > $balance) {
            return back()->with('error', 'Not enough balance in fund!')
                         ->withInput();
        }

        // আগে expense এন্ট্রি
        $expense = Expense::create([
            'title'        => $validated['title'],
            'amount'       => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'category'     => $validated['category'] ?? null,
            'note'         => $validated['note'] ?? null,
            'created_by'   => Auth::id(),
        ]);

        // তারপর ফান্ড থেকে out ট্রানজ্যাকশন
        $fund = FundTransaction::create([
            'direction' => 'out',
            'source'    => 'expense',
            'source_id' => $expense->id,
            'amount'    => $expense->amount,
            'note'      => 'Expense: ' . $expense->title . ($expense->note ? ' - ' . $expense->note : ''),
            'created_by'=> Auth::id(),
        ]);

        // লিঙ্ক আপডেট
        $expense->update([
            'fund_transaction_id' => $fund->id,
        ]);

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Expense saved successfully!');
    }

    // ✅ Edit ফর্ম
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);

        // উপরে summary একই থাকবে
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        $balance   = $total_in - $total_out;

        $today        = Carbon::today();
        $currentYear  = $today->year;
        $currentMonth = $today->month;

        $yearlyExpense = Expense::whereYear('expense_date', $currentYear)->sum('amount');
        $monthlyExpense = Expense::whereYear('expense_date', $currentYear)
                            ->whereMonth('expense_date', $currentMonth)
                            ->sum('amount');
        $todayExpense = Expense::whereDate('expense_date', $today)->sum('amount');

        $expenses = Expense::orderBy('expense_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(20);

        return view('backEnd.expenses.edit', compact(
            'expense',
            'balance',
            'currentYear',
            'currentMonth',
            'yearlyExpense',
            'monthlyExpense',
            'todayExpense',
            'expenses'
        ));
    }

    // ✅ Update Expense
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category'     => 'nullable|string|max:100',
            'note'         => 'nullable|string',
        ]);

        $expense = Expense::findOrFail($id);

        $expense->update([
            'title'        => $validated['title'],
            'amount'       => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'category'     => $validated['category'] ?? null,
            'note'         => $validated['note'] ?? null,
            'updated_by'   => Auth::id(),
        ]);

        // লিঙ্কড ফান্ড ট্রানজ্যাকশন আপডেট
        if ($expense->fund_transaction_id) {
            $fund = FundTransaction::find($expense->fund_transaction_id);

            if ($fund) {
                $fund->amount = $expense->amount;
                $fund->note   = 'Expense: ' . $expense->title . ($expense->note ? ' - ' . $expense->note : '');
                $fund->save();
            }
        }

        return redirect()->route('admin.expenses.index')
                         ->with('success', 'Expense updated successfully!');
    }

    // ✅ Export CSV
    public function export(Request $request)
    {
        $from = $request->input('from_date');
        $to   = $request->input('to_date');

        $query = Expense::orderBy('expense_date', 'asc');

        if ($from) {
            $query->whereDate('expense_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('expense_date', '<=', $to);
        }

        $expenses = $query->get();

        $fileName = 'expenses_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($expenses) {
            $handle = fopen('php://output', 'w');

            // হেডার
            fputcsv($handle, ['Date', 'Title', 'Category', 'Amount', 'Note']);

            foreach ($expenses as $exp) {
                fputcsv($handle, [
                    $exp->expense_date,
                    $exp->title,
                    $exp->category,
                    $exp->amount,
                    $exp->note,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

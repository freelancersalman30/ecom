<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FundTransaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FundController extends Controller
{
    /**
     * ফান্ড ড্যাশবোর্ড + হিস্টরি লিস্ট
     */
    public function index(Request $request)
    {
        // 👉 হিস্টরি লিস্ট কুয়েরি (চাইলে ফিল্টার যোগ করতে পারিস)
        $query = FundTransaction::orderBy('created_at', 'desc');

        // যদি index পেইজেও তারিখ অনুযায়ী ফিল্টার করতে চাস:
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->paginate(20);

        // মোট ইন / আউট / ব্যালেন্স
        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        $balance   = $total_in - $total_out;

        // টাইম রিলেটেড
        $now          = Carbon::now();
        $currentYear  = $now->year;
        $currentMonth = $now->month;

        // ✅ এই বছর যত ইনকাম হয়েছে (direction = 'in')
        $yearlyAdded = FundTransaction::where('direction', 'in')
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        // ✅ এই মাসে যত ইনকাম হয়েছে
        $monthlyAdded = FundTransaction::where('direction', 'in')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('amount');

        return view('backEnd.fund.index', compact(
            'balance',
            'transactions',
            'total_in',
            'total_out',
            'yearlyAdded',
            'monthlyAdded',
            'currentYear',
            'currentMonth'
        ));
    }

    /**
     * ফান্ড Add
     */
    public function add(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'note'   => 'nullable|string'
        ]);

        FundTransaction::create([
            'direction'  => 'in',            // টাকা ঢুকছে
            'source'     => 'manual_add',    // ম্যানুয়ালি অ্যাড
            'source_id'  => null,
            'amount'     => $request->amount,
            'note'       => $request->note,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Fund added successfully!');
    }

    /**
     * ফান্ড Withdraw
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'note'   => 'nullable|string'
        ]);

        $total_in  = FundTransaction::where('direction', 'in')->sum('amount');
        $total_out = FundTransaction::where('direction', 'out')->sum('amount');
        $balance   = $total_in - $total_out;

        if ($request->amount > $balance) {
            return back()->with('error', 'Not enough balance!');
        }

        FundTransaction::create([
            'direction'  => 'out',          // টাকা বের হচ্ছে
            'source'     => 'withdraw',
            'source_id'  => null,
            'amount'     => $request->amount,
            'note'       => $request->note,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Withdraw successful!');
    }

    /**
     * ফান্ড হিস্টরি Export (CSV)
     * filter = year | month | custom
     */
    public function export(Request $request)
    {
        $filter = $request->input('filter'); // year / month / custom

        $query = FundTransaction::orderBy('created_at', 'asc');

        if ($filter === 'year') {
            // 👉 শুধুমাত্র নির্দিষ্ট বছরের রিপোর্ট
            $year = $request->input('year', now()->year);
            $query->whereYear('created_at', $year);

        } elseif ($filter === 'month') {
            // 👉 নির্দিষ্ট বছর + মাস
            $year  = $request->input('year', now()->year);
            $month = $request->input('month', now()->month);

            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);

        } else {
            // 👉 Custom date range
            $request->validate([
                'from_date' => 'nullable|date',
                'to_date'   => 'nullable|date',
            ]);

            if ($request->from_date) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
        }

        $transactions = $query->get();

        $fileName = 'fund-history-'.now()->format('Y-m-d-H-i-s').'.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $columns = ['Date', 'Direction', 'Source', 'Amount', 'Note'];

        return response()->streamDownload(function () use ($transactions, $columns) {
            $handle = fopen('php://output', 'w');

            // হেডার লিখি
            fputcsv($handle, $columns);

            foreach ($transactions as $tx) {
                fputcsv($handle, [
                    $tx->created_at->format('Y-m-d H:i'),
                    $tx->direction == 'in' ? 'In (+)' : 'Out (-)',
                    $tx->source,
                    $tx->amount,
                    $tx->note,
                ]);
            }

            fclose($handle);
        }, $fileName, $headers);
    }
}

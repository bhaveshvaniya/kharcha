<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Transaction::forUser($user->id);

        $this->applyFilters($query, $request);

        $transactions = $query->orderBy('date', 'desc')->get();
        $categories   = Transaction::categories();

        $totalIncome  = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance   = $totalIncome - $totalExpense;
        $savingsRate  = $totalIncome > 0 ? round(($netBalance / $totalIncome) * 100, 1) : 0;

        // Category summary
        $expenseByCat = $transactions->where('type', 'expense')
            ->groupBy('category')
            ->map(fn($g) => $g->sum('amount'))
            ->sortByDesc(fn($v) => $v);

        $incomeByCat = $transactions->where('type', 'income')
            ->groupBy('category')
            ->map(fn($g) => $g->sum('amount'))
            ->sortByDesc(fn($v) => $v);

        // Monthly summary for chart
        $monthlySummary = $transactions->groupBy(fn($t) => $t->date->format('Y-m'))
            ->map(fn($g) => [
                'income'  => $g->where('type', 'income')->sum('amount'),
                'expense' => $g->where('type', 'expense')->sum('amount'),
            ])->sortKeys();

        return view('reports.index', compact(
            'transactions', 'categories', 'totalIncome', 'totalExpense',
            'netBalance', 'savingsRate', 'expenseByCat', 'incomeByCat', 'monthlySummary'
        ));
    }

    public function downloadCsv(Request $request)
    {
        $user  = Auth::user();
        $query = Transaction::forUser($user->id);
        $this->applyFilters($query, $request);
        $transactions = $query->orderBy('date', 'desc')->get();

        $filename = 'kharcha_report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Description', 'Category', 'Type', 'Amount (₹)', 'Note']);
            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->date->format('d/m/Y'),
                    $t->description,
                    $t->category,
                    ucfirst($t->type),
                    ($t->type === 'income' ? '+' : '-') . number_format($t->amount, 2),
                    $t->note ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadJson(Request $request)
    {
        $user  = Auth::user();
        $query = Transaction::forUser($user->id);
        $this->applyFilters($query, $request);
        $transactions = $query->orderBy('date', 'desc')->get();

        $income  = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');

        $data = [
            'generated_at' => now()->toISOString(),
            'generated_by' => $user->name,
            'summary'      => [
                'total_income'  => $income,
                'total_expense' => $expense,
                'net_balance'   => $income - $expense,
                'count'         => $transactions->count(),
            ],
            'transactions' => $transactions->map(fn($t) => [
                'id'          => $t->id,
                'date'        => $t->date->format('Y-m-d'),
                'description' => $t->description,
                'category'    => $t->category,
                'type'        => $t->type,
                'amount'      => $t->amount,
                'note'        => $t->note,
            ]),
        ];

        $filename = 'kharcha_report_' . now()->format('Y-m-d') . '.json';

        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function downloadPdf(Request $request)
    {
        $user  = Auth::user();
        $query = Transaction::forUser($user->id);
        $this->applyFilters($query, $request);
        $transactions = $query->orderBy('date', 'desc')->get();

        $income   = $transactions->where('type', 'income')->sum('amount');
        $expense  = $transactions->where('type', 'expense')->sum('amount');
        $balance  = $income - $expense;

        $html = view('reports.pdf', compact('transactions', 'income', 'expense', 'balance', 'user'))->render();

        // Simple HTML-to-download (requires dompdf if you want real PDF)
        // For now return styled HTML with print styles
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="kharcha_report_' . now()->format('Y-m-d') . '.html"');
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }
        if ($request->filled('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('description', 'like', "%{$s}%")->orWhere('note', 'like', "%{$s}%"));
        }
        if ($request->filled('quick_range')) {
            $this->applyQuickRange($query, $request->quick_range);
        }
    }

    private function applyQuickRange($query, $range)
    {
        $now = now();
        match ($range) {
            'this_month'  => $query->whereYear('date', $now->year)->whereMonth('date', $now->month),
            'last_month'  => $query->whereYear('date', $now->copy()->subMonth()->year)->whereMonth('date', $now->copy()->subMonth()->month),
            'this_year'   => $query->whereYear('date', $now->year),
            'last_3'      => $query->whereDate('date', '>=', $now->copy()->subMonths(3)->startOfMonth()),
            'last_6'      => $query->whereDate('date', '>=', $now->copy()->subMonths(6)->startOfMonth()),
            default       => null,
        };
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $year   = $request->get('year', now()->year);
        $month  = $request->get('month', now()->month);

        // Current month stats
        $monthTx   = Transaction::forUser($user->id)->forMonth($year, $month)->get();
        $income    = $monthTx->where('type', 'income')->sum('amount');
        $expense   = $monthTx->where('type', 'expense')->sum('amount');
        $balance   = $income - $expense;
        $savingsRate = $income > 0 ? round(($balance / $income) * 100, 1) : 0;

        // Recent transactions
        $recentTransactions = Transaction::forUser($user->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Category breakdown for current month (expenses only)
        $categoryBreakdown = Transaction::forUser($user->id)
            ->forMonth($year, $month)
            ->expense()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Last 6 months chart data
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $d  = now()->subMonths($i);
            $tx = Transaction::forUser($user->id)->forMonth($d->year, $d->month)->get();
            $chartData[] = [
                'label'   => $d->format('M Y'),
                'income'  => $tx->where('type', 'income')->sum('amount'),
                'expense' => $tx->where('type', 'expense')->sum('amount'),
            ];
        }

        return view('dashboard.index', compact(
            'income', 'expense', 'balance', 'savingsRate',
            'recentTransactions', 'categoryBreakdown', 'chartData',
            'year', 'month'
        ));
    }
}

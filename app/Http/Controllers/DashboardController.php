<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\PortfolioHolding;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $holdings = PortfolioHolding::with('stock')->get();

        $totalInvested = $holdings->sum('total_invested');
        $totalCurrentValue = $holdings->sum(fn($h) => $h->current_value);
        $totalProfitLoss = $totalCurrentValue - $totalInvested;
        $totalProfitLossPercent = $totalInvested > 0 ? ($totalProfitLoss / $totalInvested) * 100 : 0;

        $topGainers = Stock::where('is_active', true)
            ->whereColumn('current_price', '>', 'previous_close')
            ->orderByRaw('((current_price - previous_close) / previous_close) DESC')
            ->limit(5)->get();

        $topLosers = Stock::where('is_active', true)
            ->whereColumn('current_price', '<', 'previous_close')
            ->orderByRaw('((current_price - previous_close) / previous_close) ASC')
            ->limit(5)->get();

        $recentTransactions = Transaction::with('stock')->latest()->limit(10)->get();

        return view('dashboard.index', compact(
            'holdings', 'totalInvested', 'totalCurrentValue',
            'totalProfitLoss', 'totalProfitLossPercent',
            'topGainers', 'topLosers', 'recentTransactions'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\PortfolioHolding;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index()
    {
        $holdings = PortfolioHolding::with('stock')->get();
        $totalInvested = $holdings->sum('total_invested');
        $totalCurrentValue = $holdings->sum(fn($h) => $h->current_value);
        $totalPL = $totalCurrentValue - $totalInvested;
        $totalPLPercent = $totalInvested > 0 ? ($totalPL / $totalInvested) * 100 : 0;

        return view('portfolio.index', compact('holdings', 'totalInvested', 'totalCurrentValue', 'totalPL', 'totalPLPercent'));
    }

    public function buy(Request $request)
    {
        $validated = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|integer|min:1',
            'price_per_share' => 'required|numeric|min:0',
            'brokerage' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $totalAmount = $validated['quantity'] * $validated['price_per_share'];
        $brokerage = $validated['brokerage'] ?? 0;

        // Create transaction
        Transaction::create([
            'stock_id' => $validated['stock_id'],
            'type' => 'buy',
            'quantity' => $validated['quantity'],
            'price_per_share' => $validated['price_per_share'],
            'total_amount' => $totalAmount,
            'brokerage' => $brokerage,
            'transaction_date' => $validated['transaction_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update or create holding
        $holding = PortfolioHolding::where('stock_id', $validated['stock_id'])->first();
        if ($holding) {
            $newQty = $holding->quantity + $validated['quantity'];
            $newInvested = $holding->total_invested + $totalAmount + $brokerage;
            $holding->update([
                'quantity' => $newQty,
                'average_buy_price' => $newInvested / $newQty,
                'total_invested' => $newInvested,
            ]);
        } else {
            PortfolioHolding::create([
                'stock_id' => $validated['stock_id'],
                'quantity' => $validated['quantity'],
                'average_buy_price' => $validated['price_per_share'],
                'total_invested' => $totalAmount + $brokerage,
                'first_purchase_date' => $validated['transaction_date'],
                'notes' => $validated['notes'] ?? null,
            ]);
        }

        return redirect()->route('portfolio.index')->with('success', 'Buy order recorded successfully.');
    }

    public function sell(Request $request)
    {
        $validated = $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|integer|min:1',
            'price_per_share' => 'required|numeric|min:0',
            'brokerage' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $holding = PortfolioHolding::where('stock_id', $validated['stock_id'])->firstOrFail();

        if ($holding->quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'You do not have enough shares to sell.'])->withInput();
        }

        $totalAmount = $validated['quantity'] * $validated['price_per_share'];
        $brokerage = $validated['brokerage'] ?? 0;

        Transaction::create([
            'stock_id' => $validated['stock_id'],
            'type' => 'sell',
            'quantity' => $validated['quantity'],
            'price_per_share' => $validated['price_per_share'],
            'total_amount' => $totalAmount,
            'brokerage' => $brokerage,
            'transaction_date' => $validated['transaction_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $newQty = $holding->quantity - $validated['quantity'];
        if ($newQty == 0) {
            $holding->delete();
        } else {
            $holding->update([
                'quantity' => $newQty,
                'total_invested' => $holding->average_buy_price * $newQty,
            ]);
        }

        return redirect()->route('portfolio.index')->with('success', 'Sell order recorded successfully.');
    }

    public function transactions()
    {
        $transactions = Transaction::with('stock')->latest('transaction_date')->paginate(20);
        return view('portfolio.transactions', compact('transactions'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with('latestPrice')->where('is_active', true)->latest()->get();
        return view('stocks.index', compact('stocks'));
    }

    public function create()
    {
        return view('stocks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'symbol' => 'required|string|max:20|unique:stocks,symbol',
            'company_name' => 'required|string|max:255',
            'sector' => 'nullable|string|max:100',
            'exchange' => 'required|string|max:20',
            'current_price' => 'required|numeric|min:0',
            'previous_close' => 'required|numeric|min:0',
        ]);

        $stock = Stock::create($validated);

        return redirect()->route('stocks.index')->with('success', "Stock {$stock->symbol} added successfully.");
    }

    public function show(Stock $stock)
    {
        $prices = $stock->prices()->orderBy('price_date', 'desc')->limit(30)->get();
        $holding = $stock->holding;
        $transactions = $stock->transactions()->latest()->get();

        // Chart data
        $chartLabels = $prices->pluck('price_date')->map(fn($d) => $d->format('d M'))->reverse()->values();
        $highData = $prices->pluck('high_price')->reverse()->values();
        $lowData = $prices->pluck('low_price')->reverse()->values();
        $closeData = $prices->pluck('close_price')->reverse()->values();

        return view('stocks.show', compact('stock', 'prices', 'holding', 'transactions', 'chartLabels', 'highData', 'lowData', 'closeData'));
    }

    public function edit(Stock $stock)
    {
        return view('stocks.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'sector' => 'nullable|string|max:100',
            'exchange' => 'required|string|max:20',
            'current_price' => 'required|numeric|min:0',
            'previous_close' => 'required|numeric|min:0',
        ]);

        $stock->update($validated);

        return redirect()->route('stocks.index')->with('success', "Stock {$stock->symbol} updated successfully.");
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Stock deleted.');
    }

    // Update daily price (high/low)
    public function updatePrice(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'price_date' => 'required|date',
            'open_price' => 'nullable|numeric|min:0',
            'high_price' => 'nullable|numeric|min:0',
            'low_price' => 'nullable|numeric|min:0',
            'close_price' => 'nullable|numeric|min:0',
            'volume' => 'nullable|integer|min:0',
        ]);

        $previousPrice = $stock->prices()
            ->where('price_date', '<', $validated['price_date'])
            ->orderBy('price_date', 'desc')->first();

        $prevClose = $previousPrice ? $previousPrice->close_price : $stock->previous_close;
        $changeAmount = ($validated['close_price'] ?? 0) - $prevClose;
        $changePercent = $prevClose > 0 ? ($changeAmount / $prevClose) * 100 : 0;

        $validated['volume'] = $validated['volume'] ?? 0;
        $validated['change_amount'] = $changeAmount;
        $validated['change_percent'] = $changePercent;

        $stockPrice = StockPrice::updateOrCreate(
            ['stock_id' => $stock->id, 'price_date' => $validated['price_date']],
            $validated
        );

        // Update stock's current price if it's today's entry
        if ($validated['price_date'] == today()->toDateString() && isset($validated['close_price'])) {
            $stock->update([
                'previous_close' => $prevClose,
                'current_price' => $validated['close_price'],
            ]);
        }

        return redirect()->route('stocks.show', $stock)->with('success', 'Price updated successfully.');
    }
}

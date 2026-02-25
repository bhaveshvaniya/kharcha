<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\StockPrice;
use App\Models\PortfolioHolding;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $stocks = [
            ['symbol' => 'RELIANCE', 'company_name' => 'Reliance Industries Ltd', 'sector' => 'Energy', 'exchange' => 'NSE', 'current_price' => 2950.50, 'previous_close' => 2920.00],
            ['symbol' => 'TCS', 'company_name' => 'Tata Consultancy Services', 'sector' => 'IT', 'exchange' => 'NSE', 'current_price' => 3850.75, 'previous_close' => 3900.00],
            ['symbol' => 'INFY', 'company_name' => 'Infosys Ltd', 'sector' => 'IT', 'exchange' => 'NSE', 'current_price' => 1620.30, 'previous_close' => 1590.00],
            ['symbol' => 'HDFCBANK', 'company_name' => 'HDFC Bank Ltd', 'sector' => 'Banking', 'exchange' => 'NSE', 'current_price' => 1720.00, 'previous_close' => 1700.00],
            ['symbol' => 'WIPRO', 'company_name' => 'Wipro Ltd', 'sector' => 'IT', 'exchange' => 'NSE', 'current_price' => 445.60, 'previous_close' => 460.00],
            ['symbol' => 'SBIN', 'company_name' => 'State Bank of India', 'sector' => 'Banking', 'exchange' => 'NSE', 'current_price' => 625.80, 'previous_close' => 610.00],
        ];

        foreach ($stocks as $data) {
            $stock = Stock::create($data);

            // Generate 30 days of price history
            for ($i = 30; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $base = $data['previous_close'];
                $variation = $base * 0.03;
                $open = round($base + rand(-100, 100) / 100 * $variation, 2);
                $close = round($base + rand(-150, 150) / 100 * $variation, 2);
                $high = round(max($open, $close) + rand(0, 100) / 100 * $variation * 0.5, 2);
                $low = round(min($open, $close) - rand(0, 100) / 100 * $variation * 0.5, 2);

                $prevClose = $i == 30 ? $base : StockPrice::where('stock_id', $stock->id)
                    ->orderBy('price_date', 'desc')->value('close_price') ?? $base;

                StockPrice::create([
                    'stock_id' => $stock->id,
                    'price_date' => $date->toDateString(),
                    'open_price' => $open,
                    'high_price' => $high,
                    'low_price' => $low,
                    'close_price' => $close,
                    'volume' => rand(500000, 5000000),
                    'change_amount' => $close - $prevClose,
                    'change_percent' => $prevClose > 0 ? (($close - $prevClose) / $prevClose) * 100 : 0,
                ]);
            }
        }

        // Sample portfolio holdings
        $reliance = Stock::where('symbol', 'RELIANCE')->first();
        $tcs = Stock::where('symbol', 'TCS')->first();
        $infy = Stock::where('symbol', 'INFY')->first();

        PortfolioHolding::create([
            'stock_id' => $reliance->id,
            'quantity' => 50,
            'average_buy_price' => 2750.00,
            'total_invested' => 137500,
            'first_purchase_date' => '2024-01-15',
        ]);

        PortfolioHolding::create([
            'stock_id' => $tcs->id,
            'quantity' => 20,
            'average_buy_price' => 3600.00,
            'total_invested' => 72000,
            'first_purchase_date' => '2024-03-10',
        ]);

        PortfolioHolding::create([
            'stock_id' => $infy->id,
            'quantity' => 100,
            'average_buy_price' => 1500.00,
            'total_invested' => 150000,
            'first_purchase_date' => '2024-02-20',
        ]);

        // Sample transactions
        Transaction::create(['stock_id' => $reliance->id, 'type' => 'buy', 'quantity' => 50, 'price_per_share' => 2750, 'total_amount' => 137500, 'brokerage' => 250, 'transaction_date' => '2024-01-15']);
        Transaction::create(['stock_id' => $tcs->id, 'type' => 'buy', 'quantity' => 20, 'price_per_share' => 3600, 'total_amount' => 72000, 'brokerage' => 150, 'transaction_date' => '2024-03-10']);
        Transaction::create(['stock_id' => $infy->id, 'type' => 'buy', 'quantity' => 100, 'price_per_share' => 1500, 'total_amount' => 150000, 'brokerage' => 300, 'transaction_date' => '2024-02-20']);
    }
}

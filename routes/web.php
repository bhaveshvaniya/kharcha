<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PortfolioController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('stocks', StockController::class);
Route::post('stocks/{stock}/update-price', [StockController::class, 'updatePrice'])->name('stocks.update-price');

Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/', [PortfolioController::class, 'index'])->name('index');
    Route::post('/buy', [PortfolioController::class, 'buy'])->name('buy');
    Route::post('/sell', [PortfolioController::class, 'sell'])->name('sell');
    Route::get('/transactions', [PortfolioController::class, 'transactions'])->name('transactions');
    Route::get('/task', [PortfolioController::class, 'task'])->name('task');
});

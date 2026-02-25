@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Summary Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-blue-soft"><i class="bi bi-wallet2"></i></div>
                <div>
                    <div class="text-muted small">Total Invested</div>
                    <div class="fw-bold fs-5">₹{{ number_format($totalInvested, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-green-soft"><i class="bi bi-graph-up"></i></div>
                <div>
                    <div class="text-muted small">Current Value</div>
                    <div class="fw-bold fs-5">₹{{ number_format($totalCurrentValue, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon {{ $totalProfitLoss >= 0 ? 'bg-green-soft' : 'bg-red-soft' }}">
                    <i class="bi bi-{{ $totalProfitLoss >= 0 ? 'trending-up' : 'trending-down' }}"></i>
                </div>
                <div>
                    <div class="text-muted small">Total P&L</div>
                    <div class="fw-bold fs-5 {{ $totalProfitLoss >= 0 ? 'text-green' : 'text-red' }}">
                        {{ $totalProfitLoss >= 0 ? '+' : '' }}₹{{ number_format($totalProfitLoss, 2) }}
                    </div>
                    <small class="{{ $totalProfitLoss >= 0 ? 'text-green' : 'text-red' }}">
                        {{ $totalProfitLossPercent >= 0 ? '+' : '' }}{{ number_format($totalProfitLossPercent, 2) }}%
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-yellow-soft"><i class="bi bi-briefcase"></i></div>
                <div>
                    <div class="text-muted small">Holdings</div>
                    <div class="fw-bold fs-5">{{ $holdings->count() }} Stocks</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Top Gainers -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-graph-up-arrow text-success me-2"></i>Top Gainers</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th class="ps-3">Symbol</th><th>Price</th><th>Change</th></tr></thead>
                        <tbody>
                        @forelse($topGainers as $stock)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('stocks.show', $stock) }}" class="text-decoration-none">
                                    <span class="stock-symbol">{{ $stock->symbol }}</span>
                                </a>
                                <div class="small text-muted">{{ Str::limit($stock->company_name, 25) }}</div>
                            </td>
                            <td class="fw-semibold">₹{{ number_format($stock->current_price, 2) }}</td>
                            <td><span class="badge badge-up rounded-pill">+{{ number_format($stock->change_percent, 2) }}%</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted p-4">No data available</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Losers -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-graph-down-arrow text-danger me-2"></i>Top Losers</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th class="ps-3">Symbol</th><th>Price</th><th>Change</th></tr></thead>
                        <tbody>
                        @forelse($topLosers as $stock)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('stocks.show', $stock) }}" class="text-decoration-none">
                                    <span class="stock-symbol">{{ $stock->symbol }}</span>
                                </a>
                                <div class="small text-muted">{{ Str::limit($stock->company_name, 25) }}</div>
                            </td>
                            <td class="fw-semibold">₹{{ number_format($stock->current_price, 2) }}</td>
                            <td><span class="badge badge-down rounded-pill">{{ number_format($stock->change_percent, 2) }}%</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted p-4">No data available</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Holdings Table -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-briefcase me-2"></i>Portfolio Holdings</span>
        <a href="{{ route('portfolio.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Stock</th>
                        <th>Qty</th>
                        <th>Avg Price</th>
                        <th>Current Price</th>
                        <th>Invested</th>
                        <th>Current Value</th>
                        <th>P&L</th>
                        <th>P&L %</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($holdings as $h)
                <tr>
                    <td class="ps-3">
                        <a href="{{ route('stocks.show', $h->stock) }}" class="text-decoration-none">
                            <span class="stock-symbol">{{ $h->stock->symbol }}</span>
                        </a>
                        <div class="small text-muted">{{ Str::limit($h->stock->company_name, 20) }}</div>
                    </td>
                    <td>{{ $h->quantity }}</td>
                    <td>₹{{ number_format($h->average_buy_price, 2) }}</td>
                    <td>₹{{ number_format($h->stock->current_price, 2) }}</td>
                    <td>₹{{ number_format($h->total_invested, 2) }}</td>
                    <td>₹{{ number_format($h->current_value, 2) }}</td>
                    <td class="{{ $h->profit_loss >= 0 ? 'change-up' : 'change-down' }}">
                        {{ $h->profit_loss >= 0 ? '+' : '' }}₹{{ number_format($h->profit_loss, 2) }}
                    </td>
                    <td class="{{ $h->profit_loss_percent >= 0 ? 'change-up' : 'change-down' }}">
                        {{ $h->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($h->profit_loss_percent, 2) }}%
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted p-4">No holdings yet. <a href="{{ route('portfolio.index') }}">Buy your first stock!</a></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-receipt me-2"></i>Recent Transactions</span>
        <a href="{{ route('portfolio.transactions') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Stock</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentTransactions as $t)
                <tr>
                    <td class="ps-3">{{ $t->transaction_date->format('d M Y') }}</td>
                    <td><span class="stock-symbol">{{ $t->stock->symbol }}</span></td>
                    <td>
                        <span class="badge {{ $t->type == 'buy' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            {{ strtoupper($t->type) }}
                        </span>
                    </td>
                    <td>{{ $t->quantity }}</td>
                    <td>₹{{ number_format($t->price_per_share, 2) }}</td>
                    <td class="fw-semibold">₹{{ number_format($t->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted p-4">No transactions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

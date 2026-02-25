@extends('layouts.app')
@section('title', 'Portfolio')
@section('page-title', 'My Portfolio')

@section('content')
<!-- Summary -->
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
                <div class="icon bg-green-soft"><i class="bi bi-currency-rupee"></i></div>
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
                <div class="icon {{ $totalPL >= 0 ? 'bg-green-soft' : 'bg-red-soft' }}">
                    <i class="bi bi-{{ $totalPL >= 0 ? 'trending-up' : 'trending-down' }}"></i>
                </div>
                <div>
                    <div class="text-muted small">Total P&L</div>
                    <div class="fw-bold fs-5 {{ $totalPL >= 0 ? 'text-green' : 'text-red' }}">
                        {{ $totalPL >= 0 ? '+' : '' }}₹{{ number_format($totalPL, 2) }}
                    </div>
                    <small class="{{ $totalPL >= 0 ? 'text-green' : 'text-red' }}">
                        {{ number_format($totalPLPercent, 2) }}%
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-yellow-soft"><i class="bi bi-pie-chart"></i></div>
                <div>
                    <div class="text-muted small">Stocks Held</div>
                    <div class="fw-bold fs-5">{{ $holdings->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Trade Bar -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body d-flex gap-3 flex-wrap align-items-center">
        <span class="fw-semibold text-dark me-2"><i class="bi bi-lightning-charge-fill text-warning"></i> Quick Trade:</span>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quickBuyModal">
            <i class="bi bi-cart-plus me-1"></i> Buy Stock
        </button>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#quickSellModal">
            <i class="bi bi-cart-dash me-1"></i> Sell Stock
        </button>
        <a href="{{ route('portfolio.transactions') }}" class="btn btn-outline-secondary ms-auto">
            <i class="bi bi-receipt me-1"></i> View All Transactions
        </a>
    </div>
</div>

<!-- Holdings Table -->
<div class="card">
    <div class="card-header"><i class="bi bi-briefcase me-2"></i>Current Holdings</div>
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
                        <th>P&L (₹)</th>
                        <th>P&L %</th>
                        <th>Since</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($holdings as $h)
                <tr>
                    <td class="ps-3">
                        <a href="{{ route('stocks.show', $h->stock) }}" class="text-decoration-none">
                            <span class="stock-symbol">{{ $h->stock->symbol }}</span>
                        </a>
                        <div class="small text-muted">{{ Str::limit($h->stock->company_name, 22) }}</div>
                    </td>
                    <td class="fw-semibold">{{ $h->quantity }}</td>
                    <td>₹{{ number_format($h->average_buy_price, 2) }}</td>
                    <td class="fw-bold">₹{{ number_format($h->stock->current_price, 2) }}</td>
                    <td>₹{{ number_format($h->total_invested, 2) }}</td>
                    <td class="fw-semibold">₹{{ number_format($h->current_value, 2) }}</td>
                    <td class="{{ $h->profit_loss >= 0 ? 'change-up' : 'change-down' }}">
                        {{ $h->profit_loss >= 0 ? '+' : '' }}₹{{ number_format($h->profit_loss, 2) }}
                    </td>
                    <td>
                        <span class="badge rounded-pill {{ $h->profit_loss >= 0 ? 'badge-up' : 'badge-down' }}">
                            {{ $h->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($h->profit_loss_percent, 2) }}%
                        </span>
                    </td>
                    <td class="small text-muted">{{ $h->first_purchase_date->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('stocks.show', $h->stock) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted p-5">
                    No holdings yet. Buy your first stock!
                </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Buy Modal -->
<div class="modal fade" id="quickBuyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-cart-plus me-2"></i>Buy Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('portfolio.buy') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Select Stock <span class="text-danger">*</span></label>
                            <select name="stock_id" class="form-select" required>
                                <option value="">Choose a stock...</option>
                                @foreach(\App\Models\Stock::where('is_active',true)->orderBy('symbol')->get() as $s)
                                    <option value="{{ $s->id }}">{{ $s->symbol }} – {{ $s->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Price/Share (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="price_per_share" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Brokerage (₹)</label>
                            <input type="number" name="brokerage" class="form-control" step="0.01" min="0" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Confirm Buy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Sell Modal -->
<div class="modal fade" id="quickSellModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-cart-dash me-2"></i>Sell Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('portfolio.sell') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Select Stock <span class="text-danger">*</span></label>
                            <select name="stock_id" class="form-select" required>
                                <option value="">Choose a holding...</option>
                                @foreach($holdings as $h)
                                    <option value="{{ $h->stock_id }}">{{ $h->stock->symbol }} – {{ $h->quantity }} shares</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Price/Share (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="price_per_share" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Brokerage (₹)</label>
                            <input type="number" name="brokerage" class="form-control" step="0.01" min="0" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-check-lg me-1"></i> Confirm Sell</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

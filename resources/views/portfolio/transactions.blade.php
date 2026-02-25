@extends('layouts.app')
@section('title', 'Transactions')
@section('page-title', 'Transaction History')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-receipt me-2"></i>All Transactions</span>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#quickBuyModal">
                <i class="bi bi-cart-plus me-1"></i> Buy
            </button>
            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#quickSellModal">
                <i class="bi bi-cart-dash me-1"></i> Sell
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Date</th>
                        <th>Stock</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Price/Share</th>
                        <th>Total Amount</th>
                        <th>Brokerage</th>
                        <th>Net Amount</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($transactions as $t)
                <tr>
                    <td class="ps-3 text-muted">{{ $t->id }}</td>
                    <td>{{ $t->transaction_date->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('stocks.show', $t->stock) }}" class="text-decoration-none">
                            <span class="stock-symbol">{{ $t->stock->symbol }}</span>
                        </a>
                        <div class="small text-muted">{{ Str::limit($t->stock->company_name, 20) }}</div>
                    </td>
                    <td>
                        <span class="badge px-3 py-1 {{ $t->type == 'buy' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                            {{ strtoupper($t->type) }}
                        </span>
                    </td>
                    <td class="fw-semibold">{{ $t->quantity }}</td>
                    <td>₹{{ number_format($t->price_per_share, 2) }}</td>
                    <td class="fw-bold">₹{{ number_format($t->total_amount, 2) }}</td>
                    <td class="text-muted">₹{{ number_format($t->brokerage, 2) }}</td>
                    <td class="fw-semibold {{ $t->type == 'buy' ? 'text-danger' : 'text-green' }}">
                        {{ $t->type == 'buy' ? '−' : '+' }}₹{{ number_format($t->total_amount + $t->brokerage, 2) }}
                    </td>
                    <td class="text-muted small">{{ $t->notes ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted p-5">No transactions recorded yet.</td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer">
        {{ $transactions->links() }}
    </div>
    @endif
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
                            <label class="form-label fw-semibold">Select Stock *</label>
                            <select name="stock_id" class="form-select" required>
                                <option value="">Choose a stock...</option>
                                @foreach(\App\Models\Stock::where('is_active',true)->orderBy('symbol')->get() as $s)
                                    <option value="{{ $s->id }}">{{ $s->symbol }} – {{ $s->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6"><label class="form-label fw-semibold">Quantity *</label><input type="number" name="quantity" class="form-control" min="1" required></div>
                        <div class="col-6"><label class="form-label fw-semibold">Price/Share (₹) *</label><input type="number" name="price_per_share" class="form-control" step="0.01" min="0" required></div>
                        <div class="col-6"><label class="form-label fw-semibold">Brokerage (₹)</label><input type="number" name="brokerage" class="form-control" step="0.01" min="0" value="0"></div>
                        <div class="col-6"><label class="form-label fw-semibold">Date *</label><input type="date" name="transaction_date" class="form-control" value="{{ today()->toDateString() }}" required></div>
                        <div class="col-12"><label class="form-label fw-semibold">Notes</label><input type="text" name="notes" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Buy</button>
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
                            <label class="form-label fw-semibold">Select Holding *</label>
                            <select name="stock_id" class="form-select" required>
                                <option value="">Choose stock to sell...</option>
                                @foreach(\App\Models\PortfolioHolding::with('stock')->get() as $h)
                                    <option value="{{ $h->stock_id }}">{{ $h->stock->symbol }} – {{ $h->quantity }} shares</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6"><label class="form-label fw-semibold">Quantity *</label><input type="number" name="quantity" class="form-control" min="1" required></div>
                        <div class="col-6"><label class="form-label fw-semibold">Price/Share (₹) *</label><input type="number" name="price_per_share" class="form-control" step="0.01" min="0" required></div>
                        <div class="col-6"><label class="form-label fw-semibold">Brokerage (₹)</label><input type="number" name="brokerage" class="form-control" step="0.01" min="0" value="0"></div>
                        <div class="col-6"><label class="form-label fw-semibold">Date *</label><input type="date" name="transaction_date" class="form-control" value="{{ today()->toDateString() }}" required></div>
                        <div class="col-12"><label class="form-label fw-semibold">Notes</label><input type="text" name="notes" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Sell</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', $stock->symbol . ' – Stock Detail')
@section('page-title', $stock->symbol . ' – ' . $stock->company_name)

@section('content')
<!-- Stock Header -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stock->symbol }}</h4>
                        <p class="text-muted mb-0">{{ $stock->company_name }} &bull; {{ $stock->exchange }} &bull; {{ $stock->sector ?? 'N/A' }}</p>
                    </div>
                    <div class="ms-4">
                        <span class="fs-3 fw-bold">₹{{ number_format($stock->current_price, 2) }}</span>
                        @php $change = $stock->change_amount; $pct = $stock->change_percent; @endphp
                        <span class="ms-2 fs-5 {{ $change >= 0 ? 'change-up' : 'change-down' }}">
                            {{ $change >= 0 ? '+' : '' }}₹{{ number_format($change, 2) }}
                            ({{ $pct >= 0 ? '+' : '' }}{{ number_format($pct, 2) }}%)
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-auto d-flex gap-2">
                @if($holding)
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#buyModal">
                        <i class="bi bi-cart-plus me-1"></i> Buy More
                    </button>
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#sellModal">
                        <i class="bi bi-cart-dash me-1"></i> Sell
                    </button>
                @else
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#buyModal">
                        <i class="bi bi-cart-plus me-1"></i> Buy
                    </button>
                @endif
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#priceModal">
                    <i class="bi bi-pencil-square me-1"></i> Update Price
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Today's Price Stats -->
@if($prices->first() && $prices->first()->price_date->isToday())
@php $tp = $prices->first(); @endphp
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card p-3 text-center"><div class="small text-muted">Open</div><div class="fw-bold fs-5">₹{{ number_format($tp->open_price, 2) }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><div class="small text-success">Day High</div><div class="fw-bold fs-5 text-green">₹{{ number_format($tp->high_price, 2) }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><div class="small text-danger">Day Low</div><div class="fw-bold fs-5 text-red">₹{{ number_format($tp->low_price, 2) }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><div class="small text-muted">Volume</div><div class="fw-bold fs-5">{{ number_format($tp->volume) }}</div></div></div>
</div>
@endif

<!-- Portfolio Holding Info -->
@if($holding)
<div class="card mb-3 border-start border-4 border-primary">
    <div class="card-body">
        <h6 class="mb-3 fw-bold text-primary"><i class="bi bi-briefcase me-2"></i>My Holding</h6>
        <div class="row g-3 text-center">
            <div class="col"><div class="small text-muted">Quantity</div><div class="fw-bold">{{ $holding->quantity }}</div></div>
            <div class="col"><div class="small text-muted">Avg Buy Price</div><div class="fw-bold">₹{{ number_format($holding->average_buy_price, 2) }}</div></div>
            <div class="col"><div class="small text-muted">Total Invested</div><div class="fw-bold">₹{{ number_format($holding->total_invested, 2) }}</div></div>
            <div class="col"><div class="small text-muted">Current Value</div><div class="fw-bold">₹{{ number_format($holding->current_value, 2) }}</div></div>
            <div class="col">
                <div class="small text-muted">P&L</div>
                <div class="fw-bold {{ $holding->profit_loss >= 0 ? 'text-green' : 'text-red' }}">
                    {{ $holding->profit_loss >= 0 ? '+' : '' }}₹{{ number_format($holding->profit_loss, 2) }}
                </div>
            </div>
            <div class="col">
                <div class="small text-muted">P&L %</div>
                <div class="fw-bold {{ $holding->profit_loss_percent >= 0 ? 'text-green' : 'text-red' }}">
                    {{ $holding->profit_loss_percent >= 0 ? '+' : '' }}{{ number_format($holding->profit_loss_percent, 2) }}%
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Chart -->
<div class="card mb-3">
    <div class="card-header">
        <i class="bi bi-activity me-2"></i>Price History – Last 30 Days
    </div>
    <div class="card-body">
        <canvas id="priceChart" style="max-height:300px;"></canvas>
    </div>
</div>

<!-- Price History Table -->
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-calendar-range me-2"></i>Daily Price Records</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Open</th>
                        <th>High</th>
                        <th>Low</th>
                        <th>Close</th>
                        <th>Volume</th>
                        <th>Change</th>
                        <th>Change %</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($prices as $p)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $p->price_date->format('d M Y') }}</td>
                    <td>₹{{ number_format($p->open_price, 2) }}</td>
                    <td class="text-green fw-semibold">₹{{ number_format($p->high_price, 2) }}</td>
                    <td class="text-red fw-semibold">₹{{ number_format($p->low_price, 2) }}</td>
                    <td class="fw-bold">₹{{ number_format($p->close_price, 2) }}</td>
                    <td>{{ number_format($p->volume) }}</td>
                    <td class="{{ $p->change_amount >= 0 ? 'change-up' : 'change-down' }}">
                        {{ $p->change_amount >= 0 ? '+' : '' }}₹{{ number_format($p->change_amount, 2) }}
                    </td>
                    <td>
                        <span class="badge rounded-pill {{ $p->change_percent >= 0 ? 'badge-up' : 'badge-down' }}">
                            {{ $p->change_percent >= 0 ? '+' : '' }}{{ number_format($p->change_percent, 2) }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted p-4">No price history. Click "Update Price" to add.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Transaction History -->
@if($transactions->count())
<div class="card">
    <div class="card-header"><i class="bi bi-receipt me-2"></i>Transaction History</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Date</th><th>Type</th><th>Qty</th><th>Price</th><th>Total</th><th>Brokerage</th><th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td class="ps-3">{{ $t->transaction_date->format('d M Y') }}</td>
                    <td><span class="badge {{ $t->type == 'buy' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ strtoupper($t->type) }}</span></td>
                    <td>{{ $t->quantity }}</td>
                    <td>₹{{ number_format($t->price_per_share, 2) }}</td>
                    <td class="fw-semibold">₹{{ number_format($t->total_amount, 2) }}</td>
                    <td>₹{{ number_format($t->brokerage, 2) }}</td>
                    <td class="text-muted small">{{ $t->notes ?? '—' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Update Price Modal -->
<div class="modal fade" id="priceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Update Daily Price – {{ $stock->symbol }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('stocks.update-price', $stock) }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="price_date" class="form-control" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Open Price (₹)</label>
                            <input type="number" name="open_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-success">High Price (₹)</label>
                            <input type="number" name="high_price" class="form-control border-success" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-danger">Low Price (₹)</label>
                            <input type="number" name="low_price" class="form-control border-danger" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Close Price (₹)</label>
                            <input type="number" name="close_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Volume</label>
                            <input type="number" name="volume" class="form-control" min="0" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Buy Modal -->
<div class="modal fade" id="buyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-cart-plus me-2"></i>Buy {{ $stock->symbol }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('portfolio.buy') }}">
                @csrf
                <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="1" required id="buyQty">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Price/Share (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="price_per_share" class="form-control" step="0.01" min="0"
                                value="{{ $stock->current_price }}" required id="buyPrice">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Brokerage (₹)</label>
                            <input type="number" name="brokerage" class="form-control" step="0.01" min="0" value="0" id="buyBrokerage">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-success mb-0 p-2">
                                Total Amount: ₹<span id="buyTotal">0.00</span>
                            </div>
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

<!-- Sell Modal -->
@if($holding)
<div class="modal fade" id="sellModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-cart-dash me-2"></i>Sell {{ $stock->symbol }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('portfolio.sell') }}">
                @csrf
                <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                <div class="modal-body">
                    <div class="alert alert-warning small mb-3">
                        Available: <strong>{{ $holding->quantity }} shares</strong> @ Avg ₹{{ number_format($holding->average_buy_price, 2) }}
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="1" max="{{ $holding->quantity }}" required id="sellQty">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Price/Share (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="price_per_share" class="form-control" step="0.01" min="0"
                                value="{{ $stock->current_price }}" required id="sellPrice">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Brokerage (₹)</label>
                            <input type="number" name="brokerage" class="form-control" step="0.01" min="0" value="0" id="sellBrokerage">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-danger mb-0 p-2">
                                Total Amount: ₹<span id="sellTotal">0.00</span>
                            </div>
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
@endif
@endsection

@push('scripts')
<script>
const labels = {!! $chartLabels !!};
const highData = {!! $highData !!};
const lowData = {!! $lowData !!};
const closeData = {!! $closeData !!};

new Chart(document.getElementById('priceChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            { label: 'High', data: highData, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.05)', tension: 0.3, pointRadius: 3 },
            { label: 'Low', data: lowData, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.05)', tension: 0.3, pointRadius: 3 },
            { label: 'Close', data: closeData, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.08)', tension: 0.3, pointRadius: 3, fill: true },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' }, tooltip: { mode: 'index' } },
        scales: {
            y: { ticks: { callback: v => '₹' + v.toLocaleString() } }
        }
    }
});

// Buy total
['buyQty','buyPrice','buyBrokerage'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => {
        const qty = parseFloat(document.getElementById('buyQty').value) || 0;
        const price = parseFloat(document.getElementById('buyPrice').value) || 0;
        document.getElementById('buyTotal').textContent = (qty * price).toFixed(2);
    });
});

// Sell total
['sellQty','sellPrice','sellBrokerage'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => {
        const qty = parseFloat(document.getElementById('sellQty').value) || 0;
        const price = parseFloat(document.getElementById('sellPrice').value) || 0;
        document.getElementById('sellTotal').textContent = (qty * price).toFixed(2);
    });
});
</script>
@endpush

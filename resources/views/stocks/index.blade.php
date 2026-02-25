@extends('layouts.app')
@section('title', 'All Stocks')
@section('page-title', 'Stock Market')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Track and manage all your stocks</p>
    <a href="{{ route('stocks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Stock
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col"><i class="bi bi-bar-chart-line me-2"></i>All Stocks ({{ $stocks->count() }})</div>
            <div class="col-auto">
                <input type="text" id="stockSearch" class="form-control form-control-sm" placeholder="Search stocks..." style="width:200px;">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="stocksTable">
                <thead>
                    <tr>
                        <th class="ps-3">Symbol</th>
                        <th>Company</th>
                        <th>Exchange</th>
                        <th>Sector</th>
                        <th>Prev Close</th>
                        <th>Current Price</th>
                        <th>Change</th>
                        <th>Change%</th>
                        <th>Today H/L</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($stocks as $stock)
                @php
                    $change = $stock->change_amount;
                    $changePct = $stock->change_percent;
                    $today = $stock->todayPrice;
                @endphp
                <tr>
                    <td class="ps-3">
                        <a href="{{ route('stocks.show', $stock) }}" class="text-decoration-none">
                            <span class="stock-symbol">{{ $stock->symbol }}</span>
                        </a>
                    </td>
                    <td>{{ $stock->company_name }}</td>
                    <td><span class="badge bg-light text-dark">{{ $stock->exchange }}</span></td>
                    <td>{{ $stock->sector ?? '—' }}</td>
                    <td>₹{{ number_format($stock->previous_close, 2) }}</td>
                    <td class="fw-bold">₹{{ number_format($stock->current_price, 2) }}</td>
                    <td class="{{ $change >= 0 ? 'change-up' : 'change-down' }}">
                        {{ $change >= 0 ? '+' : '' }}₹{{ number_format($change, 2) }}
                    </td>
                    <td>
                        <span class="badge rounded-pill {{ $changePct >= 0 ? 'badge-up' : 'badge-down' }}">
                            {{ $changePct >= 0 ? '+' : '' }}{{ number_format($changePct, 2) }}%
                        </span>
                    </td>
                    <td>
                        @if($today)
                            <span class="text-green small fw-semibold">H: ₹{{ number_format($today->high_price, 2) }}</span><br>
                            <span class="text-red small fw-semibold">L: ₹{{ number_format($today->low_price, 2) }}</span>
                        @else
                            <span class="text-muted small">Not set</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('stocks.show', $stock) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('stocks.destroy', $stock) }}" onsubmit="return confirm('Delete this stock?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center text-muted p-5">
                    No stocks added yet. <a href="{{ route('stocks.create') }}">Add your first stock!</a>
                </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('stockSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#stocksTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush

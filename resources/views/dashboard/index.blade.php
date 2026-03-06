@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('topbar-actions')
    <div class="month-nav">
        <a href="{{ route('dashboard', ['year' => $month == 1 ? $year-1 : $year, 'month' => $month == 1 ? 12 : $month-1]) }}" class="btn btn-outline btn-sm">&#8592;</a>
        <span class="month-label">{{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</span>
        <a href="{{ route('dashboard', ['year' => $month == 12 ? $year+1 : $year, 'month' => $month == 12 ? 1 : $month+1]) }}" class="btn btn-outline btn-sm">&#8594;</a>
    </div>
@endsection

@section('content')

<!-- STAT CARDS -->
<div class="cards-grid">
    <div class="stat-card stat-balance">
        <div class="stat-label">Net Balance</div>
        <div class="stat-value">₹{{ number_format($balance, 2) }}</div>
        <div class="stat-sub">{{ $balance >= 0 ? '▲ Surplus' : '▼ Deficit' }} this month</div>
    </div>
    <div class="stat-card stat-income">
        <div class="stat-label">Total Income</div>
        <div class="stat-value income">₹{{ number_format($income, 2) }}</div>
        <div class="stat-sub">This month</div>
    </div>
    <div class="stat-card stat-expense">
        <div class="stat-label">Total Expenses</div>
        <div class="stat-value expense">₹{{ number_format($expense, 2) }}</div>
        <div class="stat-sub">This month</div>
    </div>
    <div class="stat-card stat-savings">
        <div class="stat-label">Savings Rate</div>
        <div class="stat-value savings">{{ $savingsRate }}%</div>
        <div class="stat-sub">Of income saved</div>
    </div>
</div>

<div class="two-col-grid">
    <!-- RECENT TRANSACTIONS -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Recent Transactions</div>
            <a href="{{ route('transactions.index') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="tx-list">
            @forelse($recentTransactions as $tx)
                <div class="tx-item">
                    <div class="tx-icon {{ $tx->type }}">
                        {{ \App\Models\Transaction::categories()[$tx->category] ?? '💰' }}
                    </div>
                    <div class="tx-info">
                        <div class="tx-name">{{ $tx->description }}</div>
                        <div class="tx-meta">{{ $tx->category }} · {{ $tx->date->format('d M') }}</div>
                    </div>
                    <div class="tx-amount {{ $tx->type }}">
                        {{ $tx->type === 'income' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                    </div>
                    <div class="tx-actions">
                        <a href="{{ route('transactions.edit', $tx) }}" class="tx-btn">✏️</a>
                        <form method="POST" action="{{ route('transactions.destroy', $tx) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="tx-btn" onclick="return confirm('Delete this transaction?')">✕</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">💸</div>
                    <p>No transactions yet. <a href="{{ route('transactions.create') }}">Add your first one!</a></p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div>
        <!-- MINI CHART -->
        <div class="panel" style="margin-bottom:20px;">
            <div class="panel-header"><div class="panel-title">6-Month Overview</div></div>
            <div class="chart-wrap">
                <canvas id="monthlyChart" height="160"></canvas>
            </div>
        </div>

        <!-- CATEGORY BREAKDOWN -->
        <div class="panel">
            <div class="panel-header"><div class="panel-title">Expenses by Category</div></div>
            <div class="cat-list">
                @php $totalExp = $categoryBreakdown->sum('total'); @endphp
                @forelse($categoryBreakdown->take(6) as $cat)
                    @php $pct = $totalExp > 0 ? round(($cat->total / $totalExp) * 100, 1) : 0; @endphp
                    <div class="cat-item">
                        <div class="cat-row">
                            <span class="cat-name">
                                {{ \App\Models\Transaction::categories()[$cat->category] ?? '💰' }} {{ $cat->category }}
                            </span>
                            <span class="cat-amount">₹{{ number_format($cat->total, 2) }}</span>
                        </div>
                        <div class="cat-bar">
                            <div class="cat-fill" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state"><p>No expense data for this month</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = @json($chartData);
const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(d => d.label),
        datasets: [
            {
                label: 'Income',
                data: chartData.map(d => d.income),
                backgroundColor: 'rgba(96, 212, 160, 0.7)',
                borderRadius: 6,
            },
            {
                label: 'Expense',
                data: chartData.map(d => d.expense),
                backgroundColor: 'rgba(240, 96, 128, 0.7)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: '#6b7280', font: { family: 'DM Mono', size: 11 } } },
            tooltip: {
                callbacks: {
                    label: ctx => '₹' + ctx.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2})
                }
            }
        },
        scales: {
            x: { ticks: { color: '#6b7280', font: { family: 'DM Mono', size: 10 } }, grid: { color: '#e5e7eb' } },
            y: {
                ticks: {
                    color: '#6b7280',
                    font: { family: 'DM Mono', size: 10 },
                    callback: v => '₹' + (v/1000).toFixed(0) + 'k'
                },
                grid: { color: '#e5e7eb' }
            }
        }
    }
});
</script>
@endpush

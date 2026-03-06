@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Downloads')

@section('content')

<!-- FILTER PANEL -->
<div class="panel" style="margin-bottom:24px;">
    <div class="panel-header">
        <div class="panel-title">📊 Generate Report</div>
    </div>
    <form method="GET" action="{{ route('reports.index') }}" class="filter-form" id="reportForm">
        <div class="filter-grid">
            <div class="form-group">
                <label>Quick Range</label>
                <select name="quick_range" class="form-control" id="quickRange" onchange="applyQuickRange(this)">
                    <option value="">Custom Range</option>
                    <option value="this_month" {{ request('quick_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('quick_range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="last_3" {{ request('quick_range') == 'last_3' ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="last_6" {{ request('quick_range') == 'last_6' ? 'selected' : '' }}>Last 6 Months</option>
                    <option value="this_year" {{ request('quick_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div class="form-group">
                <label>From Date</label>
                <input type="date" name="date_from" id="dateFrom" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="form-group">
                <label>To Date</label>
                <input type="date" name="date_to" id="dateTo" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income Only</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense Only</option>
                </select>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach(\App\Models\Transaction::categories() as $cat => $icon)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $icon }} {{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search...">
            </div>
        </div>
        <div class="actions-row">
            <button type="submit" class="btn btn-primary">🔍 Generate Report</button>
            <a href="{{ route('reports.index') }}" class="btn btn-outline">Reset</a>
            <a href="{{ route('reports.csv', request()->query()) }}" class="btn btn-outline actions-spacer">⬇ Download CSV</a>
            <a href="{{ route('reports.json', request()->query()) }}" class="btn btn-outline">⬇ Download JSON</a>
            <a href="{{ route('reports.pdf', request()->query()) }}" class="btn btn-outline">⬇ Download HTML Report</a>
        </div>
    </form>
</div>

<!-- SUMMARY CARDS -->
<div class="cards-grid reports-summary-grid">
    <div class="stat-card stat-income">
        <div class="stat-label">Total Income</div>
        <div class="stat-value income">₹{{ number_format($totalIncome, 2) }}</div>
        <div class="stat-sub">{{ $transactions->where('type','income')->count() }} records</div>
    </div>
    <div class="stat-card stat-expense">
        <div class="stat-label">Total Expenses</div>
        <div class="stat-value expense">₹{{ number_format($totalExpense, 2) }}</div>
        <div class="stat-sub">{{ $transactions->where('type','expense')->count() }} records</div>
    </div>
    <div class="stat-card stat-balance">
        <div class="stat-label">Net Balance</div>
        <div class="stat-value {{ $netBalance >= 0 ? 'income' : 'expense' }}">₹{{ number_format($netBalance, 2) }}</div>
        <div class="stat-sub">Savings rate: {{ $savingsRate }}%</div>
    </div>
</div>

<!-- CHARTS ROW -->
@if($transactions->count() > 0)
<div class="two-col-grid reports-charts-grid">
    <div class="panel">
        <div class="panel-header"><div class="panel-title">Monthly Trend</div></div>
        <div class="chart-wrap"><canvas id="trendChart" height="200"></canvas></div>
    </div>
    <div class="panel">
        <div class="panel-header"><div class="panel-title">Expense by Category</div></div>
        <div class="chart-wrap"><canvas id="catChart" height="200"></canvas></div>
    </div>
</div>
@endif

<!-- TRANSACTIONS TABLE -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Transactions ({{ $transactions->count() }})</div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td class="mono muted" data-label="Date">{{ $tx->date->format('d M Y') }}</td>
                        <td data-label="Description"><strong>{{ $tx->description }}</strong></td>
                        <td data-label="Category">{{ $tx->category }}</td>
                        <td data-label="Type"><span class="badge badge-{{ $tx->type }}">{{ $tx->type }}</span></td>
                        <td class="mono {{ $tx->type }}" data-label="Amount">{{ $tx->type === 'income' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}</td>
                        <td class="muted small" data-label="Note">{{ $tx->note ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-cell">No transactions match the selected filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="summary-bar">
        <div class="summary-item">
            <span class="summary-label">Total Income</span>
            <span class="summary-value income">₹{{ number_format($totalIncome, 2) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Expense</span>
            <span class="summary-value expense">₹{{ number_format($totalExpense, 2) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Net Savings</span>
            <span class="summary-value {{ $netBalance >= 0 ? 'income' : 'expense' }}">₹{{ number_format($netBalance, 2) }}</span>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Quick range dates
function applyQuickRange(el) {
    const now = new Date();
    const fmt = d => d.toISOString().split('T')[0];
    let from, to = fmt(now);
    switch(el.value) {
        case 'this_month': from = fmt(new Date(now.getFullYear(), now.getMonth(), 1)); break;
        case 'last_month':
            const lm = new Date(now.getFullYear(), now.getMonth()-1, 1);
            from = fmt(lm);
            to = fmt(new Date(now.getFullYear(), now.getMonth(), 0));
            break;
        case 'last_3': from = fmt(new Date(now.getFullYear(), now.getMonth()-2, 1)); break;
        case 'last_6': from = fmt(new Date(now.getFullYear(), now.getMonth()-5, 1)); break;
        case 'this_year': from = fmt(new Date(now.getFullYear(), 0, 1)); break;
    }
    if(from) { document.getElementById('dateFrom').value = from; document.getElementById('dateTo').value = to; }
    document.getElementById('reportForm').submit();
}

// Monthly trend chart
const monthly = @json($monthlySummary);
const labels = Object.keys(monthly);
if (labels.length > 0) {
    new Chart(document.getElementById('trendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                { label: 'Income', data: labels.map(k => monthly[k].income), borderColor: '#60d4a0', backgroundColor: 'rgba(96,212,160,0.1)', tension: 0.4, fill: true },
                { label: 'Expense', data: labels.map(k => monthly[k].expense), borderColor: '#f06080', backgroundColor: 'rgba(240,96,128,0.1)', tension: 0.4, fill: true },
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { color: '#6b7280' } } },
            scales: {
                x: { ticks: { color: '#6b7280' }, grid: { color: '#e5e7eb' } },
                y: { ticks: { color: '#6b7280', callback: v => '₹' + (v/1000).toFixed(0) + 'k' }, grid: { color: '#e5e7eb' } }
            }
        }
    });
}

// Category pie chart
const catData = @json($expenseByCat);
const catKeys = Object.keys(catData);
if (catKeys.length > 0) {
    const colors = ['#f0a060','#60a0f0','#c060f0','#f0d060','#f06080','#60d4a0','#60c0d4','#a0d460','#d4a060'];
    new Chart(document.getElementById('catChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: catKeys,
            datasets: [{ data: catKeys.map(k => catData[k]), backgroundColor: colors, borderWidth: 2, borderColor: '#ffffff' }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: '#6b7280', font: { size: 11 } } },
                tooltip: { callbacks: { label: ctx => ctx.label + ': ₹' + ctx.parsed.toLocaleString('en-IN') } }
            }
        }
    });
}
</script>
@endpush






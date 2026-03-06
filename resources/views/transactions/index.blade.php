@extends('layouts.app')
@section('title', 'Transactions')
@section('page-title', 'All Transactions')

@section('content')

<!-- FILTERS -->
<div class="panel" style="margin-bottom:24px;">
    <div class="panel-header">
        <div class="panel-title">🔍 Filter Transactions</div>
        <a href="{{ route('transactions.index') }}" class="btn btn-outline btn-sm">Clear Filters</a>
    </div>
    <form method="GET" action="{{ route('transactions.index') }}" class="filter-form">
        <div class="filter-grid">
            <div class="form-group">
                <label>Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..." class="form-control">
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
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
                <label>Date Range</label>
                <select name="date_range" class="form-control">
                    <option value="">All Time</option>
                    <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('date_range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="3_months" {{ request('date_range') == '3_months' ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="6_months" {{ request('date_range') == '6_months' ? 'selected' : '' }}>Last 6 Months</option>
                    <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div class="form-group">
                <label>From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="form-group">
                <label>To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
        </div>
        <div class="actions-row">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-outline">Reset</a>
        </div>
    </form>
</div>

<!-- TABLE -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Results ({{ $transactions->total() }} records)</div>
        <div class="actions-row tight">
            <a href="{{ route('reports.csv', request()->query()) }}" class="btn btn-outline btn-sm">⬇ CSV</a>
            <a href="{{ route('reports.json', request()->query()) }}" class="btn btn-outline btn-sm">⬇ JSON</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'date','dir'=>request('dir')=='asc'?'desc':'asc']) }}" class="sort-link">Date ↕</a></th>
                    <th>Description</th>
                    <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'category','dir'=>request('dir')=='asc'?'desc':'asc']) }}" class="sort-link">Category ↕</a></th>
                    <th>Type</th>
                    <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'amount','dir'=>request('dir')=='asc'?'desc':'asc']) }}" class="sort-link">Amount ↕</a></th>
                    <th>Note</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td class="mono muted" data-label="Date">{{ $tx->date->format('d M Y') }}</td>
                        <td data-label="Description"><strong>{{ $tx->description }}</strong></td>
                        <td data-label="Category">{{ $tx->category }}</td>
                        <td data-label="Type"><span class="badge badge-{{ $tx->type }}">{{ $tx->type }}</span></td>
                        <td class="mono {{ $tx->type }}" data-label="Amount">
                            {{ $tx->type === 'income' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                        </td>
                        <td class="muted small" data-label="Note">{{ $tx->note ?: '-' }}</td>
                        <td data-label="Actions">
                            <div class="actions-row tight">
                                <a href="{{ route('transactions.edit', $tx) }}" class="btn btn-outline btn-xs">Edit</a>
                                <form method="POST" action="{{ route('transactions.destroy', $tx) }}" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Delete this transaction?')">Del</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-cell">No transactions found for selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- SUMMARY BAR -->
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
            <span class="summary-label">Net Balance</span>
            <span class="summary-value {{ ($totalIncome - $totalExpense) >= 0 ? 'income' : 'expense' }}">
                ₹{{ number_format($totalIncome - $totalExpense, 2) }}
            </span>
        </div>
    </div>

    <!-- PAGINATION -->
    <div style="padding:16px 24px;">
        {{ $transactions->links() }}
    </div>
</div>

@endsection




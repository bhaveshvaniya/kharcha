<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'StockVault') – Stock Management System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --primary: #0f172a;
            --sidebar-width: 250px;
            --accent: #6366f1;
            --green: #10b981;
            --red: #ef4444;
        }
        body { background: #f1f5f9; font-family: 'Inter', system-ui, sans-serif; }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary);
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h4 {
            color: white;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .sidebar-brand h4 span.badge-pill {
            background: var(--accent);
            font-size: 0.6rem;
            padding: 3px 7px;
            border-radius: 20px;
            font-weight: 500;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.65);
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left: 3px solid var(--accent);
        }
        .sidebar .nav-section {
            color: rgba(255,255,255,0.35);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1rem 1.5rem 0.25rem;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0;
        }
        .topbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .page-body { padding: 1.5rem; }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        .stat-card { border-radius: 12px; border: none; }
        .stat-card .icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
        .text-green { color: var(--green) !important; }
        .text-red { color: var(--red) !important; }
        .bg-green-soft { background: rgba(16,185,129,0.12); color: var(--green); }
        .bg-red-soft { background: rgba(239,68,68,0.12); color: var(--red); }
        .bg-blue-soft { background: rgba(99,102,241,0.12); color: var(--accent); }
        .bg-yellow-soft { background: rgba(234,179,8,0.12); color: #ca8a04; }
        .badge-up { background: rgba(16,185,129,0.15); color: var(--green); }
        .badge-down { background: rgba(239,68,68,0.15); color: var(--red); }
        .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600; }
        .change-up { color: var(--green); font-weight: 600; }
        .change-down { color: var(--red); font-weight: 600; }
        .stock-symbol { font-weight: 700; font-size: 0.9rem; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<!-- Sidebar -->
<nav class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="bi bi-graph-up-arrow text-indigo-400"></i> StockVault <span class="badge-pill">PRO</span></h4>
    </div>
    <div class="mt-2">
        <div class="nav-section">Overview</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section">Market</div>
        <a href="{{ route('stocks.index') }}" class="nav-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> All Stocks
        </a>
        <a href="{{ route('stocks.create') }}" class="nav-link {{ request()->routeIs('stocks.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> Add Stock
        </a>

        <div class="nav-section">Portfolio</div>
        <a href="{{ route('portfolio.index') }}" class="nav-link {{ request()->routeIs('portfolio.index') ? 'active' : '' }}">
            <i class="bi bi-briefcase"></i> My Holdings
        </a>
        <a href="{{ route('portfolio.transactions') }}" class="nav-link {{ request()->routeIs('portfolio.transactions') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Transactions
        </a>
    </div>
</nav>

<!-- Main content -->
<div class="main-content">
    <div class="topbar">
        <div>
            <h6 class="mb-0 fw-600 text-dark">@yield('page-title', 'Dashboard')</h6>
            <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-success-subtle text-success px-3 py-2">Market Open</span>
        </div>
    </div>

    <div class="page-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Kharcha</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Mono:wght@300;400;500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-text">Kharcha</span><span class="logo-dot">.</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon">📊</span>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <span class="nav-icon">💳</span>
            <span>Transactions</span>
        </a>
        <a href="{{ route('transactions.create') }}" class="nav-item {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
            <span class="nav-icon">➕</span>
            <span>Add Transaction</span>
        </a>
        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="nav-icon">📥</span>
            <span>Reports</span>
        </a>
        <a href="{{ route('goals.index') }}" class="nav-item {{ request()->routeIs('goals.*') ? 'active' : '' }}">
            <span class="nav-icon">🎯</span>
            <span>Savings Goals</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-details">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">
    <!-- Top Bar -->
    <header class="topbar">
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="topbar-actions">
            @yield('topbar-actions')
            <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">+ Add Transaction</a>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Page Content -->
    <div class="page-body">
        @yield('content')
    </div>
</main>

<nav class="mobile-nav">
    <a href="{{ route('dashboard') }}" class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span class="mobile-nav-icon">📊</span>
        <span class="mobile-nav-text">Dashboard</span>
    </a>
    <a href="{{ route('transactions.index') }}" class="mobile-nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
        <span class="mobile-nav-icon">💳</span>
        <span class="mobile-nav-text">Transactions</span>
    </a>
    <a href="{{ route('transactions.create') }}" class="mobile-nav-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
        <span class="mobile-nav-icon">➕</span>
        <span class="mobile-nav-text">Add</span>
    </a>
    <a href="{{ route('reports.index') }}" class="mobile-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <span class="mobile-nav-icon">📥</span>
        <span class="mobile-nav-text">Reports</span>
    </a>
    <a href="{{ route('goals.index') }}" class="mobile-nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}">
        <span class="mobile-nav-icon">🎯</span>
        <span class="mobile-nav-text">Goals</span>
    </a>
</nav>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>

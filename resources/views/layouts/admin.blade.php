<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Dashboard') | AUSHVERA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    @stack('styles')
</head>
<body class="admin-body">

@php
    $me = Auth::user();
    $role = $me->role ?? 'user';
    $level = $me->admin_level ?? ($role === 'super_admin' ? 'super' : 'staff');
    $isSuper   = $role === 'super_admin';
    $isManager = $isSuper || $level === 'manager';
    $isStaff   = $isManager || $level === 'staff'; // staff can view but limited
@endphp

<div class="admin-layout">
    <!-- ── Sidebar ────────────────────────────────────── -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="AUSHVERA" onerror="this.style.display='none'">
                <span>AUSHVERA</span>
            </a>
            <button class="sidebar-close" id="sidebarClose" aria-label="Close Sidebar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Role Badge --}}
        <div style="padding: 8px 20px 4px;">
            @if($isSuper)
                <span style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;padding:3px 10px;border-radius:12px;font-size:10px;font-weight:700;letter-spacing:1px;">👑 SUPER ADMIN</span>
            @elseif($level === 'manager')
                <span style="background:rgba(59,130,246,0.2);color:#60a5fa;border:1px solid rgba(59,130,246,0.3);padding:3px 10px;border-radius:12px;font-size:10px;font-weight:700;letter-spacing:1px;">🏢 MANAGER</span>
            @else
                <span style="background:rgba(107,114,128,0.2);color:#9ca3af;border:1px solid rgba(107,114,128,0.3);padding:3px 10px;border-radius:12px;font-size:10px;font-weight:700;letter-spacing:1px;">👤 STAFF</span>
            @endif
        </div>

        <nav class="sidebar-nav">
            {{-- Dashboard — All levels --}}
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>

            {{-- Products — All levels (staff: read-only via JS) --}}
            <a href="{{ route('admin.products') }}" class="sidebar-link {{ request()->routeIs('admin.products') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Products
                @if(!$isManager) <span style="font-size:9px;opacity:0.5;margin-left:auto;">View Only</span> @endif
            </a>

            {{-- Orders — All levels --}}
            <a href="{{ route('admin.orders') }}" class="sidebar-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Orders
            </a>

            {{-- Users — Manager & Super only --}}
            @if($isManager)
            <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users
            </a>
            @endif

            {{-- Reviews — Manager & Super only --}}
            @if($isManager)
            <a href="{{ route('admin.reviews') }}" class="sidebar-link {{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Reviews
            </a>
            @endif

            {{-- Categories — Manager & Super only --}}
            @if($isManager)
            <a href="{{ route('admin.categories') }}" class="sidebar-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Categories
            </a>
            @endif

            {{-- Coupons — Manager & Super only --}}
            @if($isManager)
            <a href="{{ route('admin.coupons') }}" class="sidebar-link {{ request()->routeIs('admin.coupons') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                Coupons
            </a>
            @endif

            {{-- Subscriptions — Manager & Super only --}}
            @if($isManager)
            <a href="{{ route('admin.subscriptions') }}" class="sidebar-link {{ request()->routeIs('admin.subscriptions') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Subscriptions
            </a>
            @endif

            {{-- Email Campaigns — Manager & Super only --}}
            @if($isManager)
            <a href="{{ route('admin.emails') }}" class="sidebar-link {{ request()->routeIs('admin.emails') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Email Campaigns
            </a>
            @endif

            <div class="sidebar-divider"></div>

            {{-- AI Agent — All levels --}}
            <a href="{{ url('/admin') }}" class="sidebar-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                AI Agent
            </a>

            {{-- Create Admin — Super Admin only --}}
            @if($isSuper)
            <div class="sidebar-divider"></div>
            <a href="{{ route('admin.register') }}" class="sidebar-link {{ request()->routeIs('admin.register') ? 'active' : '' }}" style="color:#f59e0b;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Create Admin
            </a>
            @endif

            <div class="sidebar-divider"></div>
            <a href="{{ route('home') }}" class="sidebar-link" target="_blank">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                View Site
            </a>
        </nav>
    </aside>

    <!-- ── Main Content ───────────────────────────── -->
    <div class="admin-content-wrap" id="adminContentWrap">
        <header class="admin-topbar">
            <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="topbar-title">@yield('title', 'Dashboard')</div>
            <div class="topbar-actions">
                <span class="topbar-admin">
                    {{ $me->name ?? 'Admin' }}
                    <small style="display:block;font-size:10px;opacity:0.6;text-transform:uppercase;letter-spacing:1px;">
                        {{ $isSuper ? 'Super Admin' : ($level === 'manager' ? 'Manager' : 'Staff') }}
                    </small>
                </span>
                <form method="POST" action="{{ route('admin.logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="topbar-logout-btn" title="Logout">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>
        <main class="admin-main">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<style>
.topbar-logout-btn {
    display: flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
    color: #fff; padding: 6px 14px; border-radius: 6px;
    font-size: 13px; cursor: pointer; transition: all 0.2s;
    font-family: 'Inter', sans-serif;
}
.topbar-logout-btn:hover { background: rgba(220,53,69,0.8); border-color: transparent; }
.topbar-actions { display:flex; align-items:center; gap:16px; }
.topbar-admin { color:rgba(255,255,255,0.8); font-size:13px; }
</style>

<script>
    // Inject session token into localStorage so API calls work after web login
    @if(session('admin_token'))
    localStorage.setItem('auth_token', '{{ session("admin_token") }}');
    @endif

    // Store admin level info for page-level permission checks
    window.ADMIN_LEVEL = '{{ $level }}';
    window.ADMIN_ROLE  = '{{ $role }}';
    window.IS_SUPER    = {{ $isSuper ? 'true' : 'false' }};
    window.IS_MANAGER  = {{ $isManager ? 'true' : 'false' }};

    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    const closeBtn = document.getElementById('sidebarClose');
    if (toggle && sidebar) toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    if (closeBtn && sidebar) closeBtn.addEventListener('click', () => sidebar.classList.remove('open'));
</script>
<script src="{{ asset('assets/js/api-config.js') }}"></script>
@stack('scripts')
</body>
</html>

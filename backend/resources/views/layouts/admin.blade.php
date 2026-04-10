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
    <div class="admin-layout">
        <!-- Sidebar -->
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
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.products') }}" class="sidebar-link {{ request()->routeIs('admin.products') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    Products
                </a>
                <a href="{{ route('admin.orders') }}" class="sidebar-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    Orders
                </a>
                <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Users
                </a>
                <a href="{{ route('admin.reviews') }}" class="sidebar-link {{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Reviews
                </a>
                <a href="{{ route('admin.categories') }}" class="sidebar-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Categories
                </a>
                <a href="{{ route('admin.coupons') }}" class="sidebar-link {{ request()->routeIs('admin.coupons') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    Coupons
                </a>
                <div class="sidebar-divider"></div>
                <a href="{{ route('admin.ai') }}" class="sidebar-link {{ request()->routeIs('admin.ai') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                    AI Agent
                </a>
                <div class="sidebar-divider"></div>
                <a href="{{ route('home') }}" class="sidebar-link" target="_blank">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    View Site
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="admin-content-wrap" id="adminContentWrap">
            <header class="admin-topbar">
                <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="topbar-title">@yield('title', 'Dashboard')</div>
                <div class="topbar-actions">
                    <span class="topbar-admin">Admin Panel</span>
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

    <script>
        // Inject session token into localStorage so API calls work after web login
        @if(session('admin_token'))
        localStorage.setItem('auth_token', '{{ session("admin_token") }}');
        @endif

        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('adminSidebar');
        const closeBtn = document.getElementById('sidebarClose');
        if (toggle && sidebar) {
            toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
        }
        if (closeBtn && sidebar) {
            closeBtn.addEventListener('click', () => sidebar.classList.remove('open'));
        }
    </script>
    @stack('scripts')
</body>
</html>

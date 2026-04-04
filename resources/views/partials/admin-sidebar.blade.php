<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div>
                <h2>AUSHVERA</h2>
                <p>Admin Panel</p>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">

        {{-- Top Level --}}
        <a href="{{ url('/admin') }}" class="nav-link top-link {{ request()->is('admin') ? 'active' : '' }}">
            <span class="nav-icon">📊</span>
            <span>Dashboard</span>
        </a>

        {{-- AI AGENT — Highlighted top-level item --}}
        <a href="{{ url('/admin/ai') }}" class="nav-link top-link ai-link {{ request()->is('admin/ai') ? 'active' : '' }}">
            <span class="nav-icon">🤖</span>
            <span>AI Agent</span>
            <span class="ai-badge">Smart</span>
        </a>

        {{-- Products --}}
        <div class="nav-section {{ request()->is('admin/products*','admin/categories','admin/inventory','admin/pricing') ? 'open' : '' }}">
            <div class="nav-section-title" onclick="toggleSection(this)">
                <span class="nav-icon">📦</span> Products
                <span class="chevron">›</span>
            </div>
            <div class="nav-section-content">
                <a href="{{ url('/admin/products') }}" class="nav-link sub {{ request()->is('admin/products') ? 'active' : '' }}">All Products</a>
                <a href="{{ url('/admin/categories') }}" class="nav-link sub {{ request()->is('admin/categories') ? 'active' : '' }}">Categories</a>
                <a href="{{ url('/admin/inventory') }}" class="nav-link sub {{ request()->is('admin/inventory') ? 'active' : '' }}">Inventory</a>
                <a href="{{ url('/admin/pricing') }}" class="nav-link sub {{ request()->is('admin/pricing') ? 'active' : '' }}">Pricing</a>
            </div>
        </div>

        {{-- Orders --}}
        <div class="nav-section {{ request()->is('admin/orders') ? 'open' : '' }}">
            <div class="nav-section-title" onclick="toggleSection(this)">
                <span class="nav-icon">🛒</span> Orders
                <span class="chevron">›</span>
            </div>
            <div class="nav-section-content">
                <a href="{{ url('/admin/orders') }}" class="nav-link sub {{ request()->is('admin/orders') ? 'active' : '' }}">All Orders</a>
            </div>
        </div>

        {{-- Users --}}
        <div class="nav-section {{ request()->is('admin/users') ? 'open' : '' }}">
            <div class="nav-section-title" onclick="toggleSection(this)">
                <span class="nav-icon">👥</span> Users
                <span class="chevron">›</span>
            </div>
            <div class="nav-section-content">
                <a href="{{ url('/admin/users') }}" class="nav-link sub {{ request()->is('admin/users') ? 'active' : '' }}">All Users</a>
            </div>
        </div>

        {{-- Marketing --}}
        <div class="nav-section {{ request()->is('admin/banners','admin/coupons','admin/subscriptions','admin/reviews') ? 'open' : '' }}">
            <div class="nav-section-title" onclick="toggleSection(this)">
                <span class="nav-icon">🎯</span> Marketing
                <span class="chevron">›</span>
            </div>
            <div class="nav-section-content">
                <a href="{{ url('/admin/banners') }}" class="nav-link sub {{ request()->is('admin/banners') ? 'active' : '' }}">Banners</a>
                <a href="{{ url('/admin/coupons') }}" class="nav-link sub {{ request()->is('admin/coupons') ? 'active' : '' }}">Coupons</a>
                <a href="{{ url('/admin/subscriptions') }}" class="nav-link sub {{ request()->is('admin/subscriptions') ? 'active' : '' }}">Subscriptions</a>
                <a href="{{ url('/admin/reviews') }}" class="nav-link sub {{ request()->is('admin/reviews') ? 'active' : '' }}">Reviews</a>
            </div>
        </div>

        {{-- Reports --}}
        <div class="nav-section {{ request()->is('admin/analytics','admin/login-history') ? 'open' : '' }}">
            <div class="nav-section-title" onclick="toggleSection(this)">
                <span class="nav-icon">📈</span> Reports
                <span class="chevron">›</span>
            </div>
            <div class="nav-section-content">
                <a href="{{ url('/admin/analytics') }}" class="nav-link sub {{ request()->is('admin/analytics') ? 'active' : '' }}">Analytics</a>
                <a href="{{ url('/admin/login-history') }}" class="nav-link sub {{ request()->is('admin/login-history') ? 'active' : '' }}">Login History</a>
            </div>
        </div>

        {{-- Admin Mgmt --}}
        <div class="nav-section {{ request()->is('admin/management','admin/credentials-generator','admin/activity-logs') ? 'open' : '' }}">
            <div class="nav-section-title" onclick="toggleSection(this)">
                <span class="nav-icon">⚙️</span> Management
                <span class="chevron">›</span>
            </div>
            <div class="nav-section-content">
                <a href="{{ url('/admin/management') }}" class="nav-link sub {{ request()->is('admin/management') ? 'active' : '' }}">Manage Admins</a>
                <a href="{{ url('/admin/credentials-generator') }}" class="nav-link sub {{ request()->is('admin/credentials-generator') ? 'active' : '' }}">ID & Password Gen</a>
                <a href="{{ url('/admin/activity-logs') }}" class="nav-link sub {{ request()->is('admin/activity-logs') ? 'active' : '' }}">Activity Logs</a>
            </div>
        </div>

        <div class="sidebar-divider"></div>

        <a href="{{ url('/') }}" class="nav-link top-link dim">
            <span class="nav-icon">🏠</span>
            <span>Back to Site</span>
        </a>

        <button onclick="adminLogout()" class="nav-link top-link dim logout-btn">
            <span class="nav-icon">🚪</span>
            <span>Logout</span>
        </button>
    </nav>
</aside>

<style>
.sidebar-logo { display:flex; align-items:center; gap:10px; }
.logo-icon { font-size:24px; }
.nav-icon { font-size:16px; min-width:20px; }
.top-link { display:flex !important; align-items:center !important; gap:10px !important; padding:11px 18px !important; border-radius:10px !important; margin:2px 8px !important; font-weight:500; transition:all .2s; }
.top-link:hover { background:rgba(255,255,255,.07) !important; }
.top-link.active { background:rgba(201,169,110,.15) !important; color:#c9a96e !important; }
.ai-link { background:linear-gradient(135deg,rgba(201,169,110,.12),rgba(139,92,246,.08)) !important; border:1px solid rgba(201,169,110,.2) !important; margin-bottom:6px !important; }
.ai-link:hover,.ai-link.active { background:linear-gradient(135deg,rgba(201,169,110,.22),rgba(139,92,246,.15)) !important; border-color:rgba(201,169,110,.4) !important; }
.ai-badge { margin-left:auto; background:linear-gradient(135deg,#c9a96e,#8b5cf6); color:#fff; font-size:9px; font-weight:700; padding:2px 7px; border-radius:20px; letter-spacing:.5px; }
.nav-section-title { display:flex; align-items:center; gap:10px; padding:10px 18px; cursor:pointer; color:rgba(255,255,255,.5); font-size:12px; font-weight:600; letter-spacing:.8px; text-transform:uppercase; transition:color .2s; }
.nav-section-title:hover { color:rgba(255,255,255,.8); }
.chevron { margin-left:auto; transition:transform .25s; font-size:16px; }
.nav-section.open .chevron { transform:rotate(90deg); }
.nav-section-content { display:none; padding:2px 0; }
.nav-section.open .nav-section-content { display:block; }
.nav-link.sub { display:block; padding:8px 18px 8px 48px; font-size:13px; color:rgba(255,255,255,.55); border-radius:8px; margin:1px 8px; transition:all .2s; }
.nav-link.sub:hover { color:#fff; background:rgba(255,255,255,.05); }
.nav-link.sub.active { color:#c9a96e; background:rgba(201,169,110,.1); }
.sidebar-divider { height:1px; background:rgba(255,255,255,.07); margin:12px 16px; }
.dim { color:rgba(255,255,255,.35) !important; }
.logout-btn { border:none; width:100%; text-align:left; cursor:pointer; background:transparent; font-family:inherit; font-size:14px; }
</style>

<script>
function toggleSection(el) {
    el.parentElement.classList.toggle('open');
}
function adminLogout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('currentUser');
    fetch('/logout', { method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content||''} })
        .catch(()=>{})
        .finally(()=>{ window.location.href = '{{ url("/") }}'; });
}
</script>

<nav class="nav-home" id="siteNav">
    <div class="nav-container">
        <!-- Left Links -->
        <div class="nav-left">
            <ul class="nav-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">HOME</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">ABOUT</a></li>
                <li><a href="{{ route('philosophy') }}" class="{{ request()->routeIs('philosophy') ? 'active' : '' }}">PHILOSOPHY</a></li>
            </ul>
        </div>

        <!-- Logo Center -->
        <a class="nav-center" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/logo.png') }}" alt="AUSHVERA Logo" class="nav-logo" onerror="this.style.display='none'">
            <span>AUSHVERA</span>
        </a>

        <!-- Right Links -->
        <div class="nav-right">
            <ul class="nav-links">
                <li><a href="{{ route('products') }}" class="{{ request()->routeIs('products') ? 'active' : '' }}">PRODUCT</a></li>
                <li><a href="{{ route('ritual') }}" class="{{ request()->routeIs('ritual') ? 'active' : '' }}">RITUAL</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">CONTACT</a></li>
            </ul>
            <!-- Cart Icon -->
            <a href="{{ route('cart') }}" class="cart-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="cart-count" id="cartBadge" style="display:none;">0</span>
            </a>
            <!-- Profile Icon -->
            <a href="{{ route('profile') }}" class="nav-icon" id="profileIcon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
                </svg>
            </a>
        </div>

        <!-- Mobile burger -->
        <button class="nav-burger" id="navBurger" aria-label="Open Menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Mobile Drawer -->
    <div class="nav-drawer" id="navDrawer">
        <div class="nav-drawer-header">
            <span>AUSHVERA</span>
            <button class="nav-drawer-close" id="navClose" aria-label="Close Menu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <ul class="nav-drawer-links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
            <li><a href="{{ route('philosophy') }}" class="{{ request()->routeIs('philosophy') ? 'active' : '' }}">Philosophy</a></li>
            <li><a href="{{ route('products') }}" class="{{ request()->routeIs('products') ? 'active' : '' }}">Product</a></li>
            <li><a href="{{ route('ritual') }}" class="{{ request()->routeIs('ritual') ? 'active' : '' }}">Ritual</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>
            <li><a href="{{ route('cart') }}" class="{{ request()->routeIs('cart') ? 'active' : '' }}">Cart</a></li>
        </ul>
    </div>
    <div class="nav-overlay" id="navOverlay"></div>
</nav>

<script>
(function() {
    const burger = document.getElementById('navBurger');
    const close = document.getElementById('navClose');
    const drawer = document.getElementById('navDrawer');
    const overlay = document.getElementById('navOverlay');
    const nav = document.getElementById('siteNav');

    function openDrawer() { drawer.classList.add('open'); overlay.classList.add('show'); document.body.style.overflow='hidden'; }
    function closeDrawer() { drawer.classList.remove('open'); overlay.classList.remove('show'); document.body.style.overflow=''; }

    if (burger) burger.addEventListener('click', openDrawer);
    if (close) close.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);

    // Scroll effect
    window.addEventListener('scroll', function() {
        nav.classList.toggle('scrolled', window.scrollY > 50);
    }, { passive: true });

    function updateCartBadge() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const count = cart.reduce((s, i) => s + (i.quantity || 1), 0);
        const el = document.getElementById('cartBadge');
        if (el) { el.textContent = count; el.style.display = count > 0 ? 'inline-flex' : 'none'; }
    }
    updateCartBadge();
    window.addEventListener('cart-updated', updateCartBadge);

    function setupAdminLinks() {
        const userStr = localStorage.getItem('currentUser');
        if(userStr) {
            try {
                const user = JSON.parse(userStr);
                if (user.role === 'admin' || user.role === 'super_admin') {
                    const profileIcon = document.getElementById('profileIcon');
                    if (profileIcon) {
                        profileIcon.href = '/admin';
                    }
                }
            } catch(e) {}
        }
    }
    setupAdminLinks();
})();
</script>

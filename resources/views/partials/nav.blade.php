<nav class="nav-{{ $navType ?? 'home' }}">
    <div class="nav-container">
        <button class="mobile-toggle" aria-label="Toggle Menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <div class="nav-left">
            <ul class="nav-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">HOME</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">ABOUT</a></li>
                <li><a href="{{ route('philosophy') }}" class="{{ request()->routeIs('philosophy') ? 'active' : '' }}">PHILOSOPHY</a></li>
            </ul>
        </div>
        <a class="nav-center" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/logo.png') }}" alt="AUSHVERA Logo" class="nav-logo">
            <span>AUSHVERA</span>
        </a>
        <div class="nav-right">
            <ul class="nav-links">
                <li><a href="{{ route('products') }}" class="{{ request()->routeIs('products') ? 'active' : '' }}">PRODUCT</a></li>
                <li><a href="{{ route('ritual') }}" class="{{ request()->routeIs('ritual') ? 'active' : '' }}">RITUAL</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">CONTACT</a></li>
            </ul>
            <a href="{{ route('cart') }}" class="cart-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="cart-count">0</span>
            </a>
            <a href="{{ route('profile') }}" class="nav-icon" id="profileIcon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
                </svg>
            </a>
        </div>
    </div>
    
    <div class="mobile-nav">
        <div class="mobile-nav-header">
            <a class="nav-center" href="{{ route('home') }}">
                <span>AUSHVERA</span>
            </a>
            <button class="close-mobile" aria-label="Close Menu">&times;</button>
        </div>
        <ul class="mobile-nav-links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">HOME</a></li>
            <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">ABOUT</a></li>
            <li><a href="{{ route('philosophy') }}" class="{{ request()->routeIs('philosophy') ? 'active' : '' }}">PHILOSOPHY</a></li>
            <li><a href="{{ route('products') }}" class="{{ request()->routeIs('products') ? 'active' : '' }}">PRODUCT</a></li>
            <li><a href="{{ route('ritual') }}" class="{{ request()->routeIs('ritual') ? 'active' : '' }}">RITUAL</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">CONTACT</a></li>
            <li><a href="{{ route('profile') }}">MY PROFILE</a></li>
        </ul>
    </div>
</nav>

<style>
.mobile-toggle {
    display: none;
    background: transparent;
    border: none;
    color: #F7F4EE;
    cursor: pointer;
}

.mobile-nav {
    position: fixed;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: #0B1C2D;
    z-index: 2000;
    transition: left 0.3s ease;
    padding: 2rem;
}

.mobile-nav.active {
    left: 0;
}

.mobile-nav-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3rem;
}

.close-mobile {
    background: transparent;
    border: none;
    color: #F7F4EE;
    font-size: 2.5rem;
    cursor: pointer;
}

.mobile-nav-links {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 2rem;
    text-align: center;
}

.mobile-nav-links a {
    color: #F7F4EE;
    text-decoration: none;
    font-size: 1.5rem;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.mobile-nav-links a.active {
    color: #B8964C;
}

@media (max-width: 968px) {
    .mobile-toggle { display: block; }
    .nav-left, .nav-right .nav-links { display: none; }
    .nav-container { grid-template-columns: auto 1fr auto; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.querySelector('.mobile-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    const closeBtn = document.querySelector('.close-mobile');
    
    if (toggle && mobileNav) {
        toggle.addEventListener('click', () => mobileNav.classList.add('active'));
    }
    
    if (closeBtn && mobileNav) {
        closeBtn.addEventListener('click', () => mobileNav.classList.remove('active'));
    }
});
</script>

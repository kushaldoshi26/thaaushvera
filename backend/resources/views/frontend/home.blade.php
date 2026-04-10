@extends('layouts.app')

@section('title', 'AUSHVERA — Wellness, Refined by Nature')
@section('description', 'Premium Ayurvedic wellness products rooted in ancient botanical wisdom, crafted for modern living.')

@section('content')

{{-- Hero Banner Slider --}}
<section class="hero">
    <div class="banner-slider">
        <div class="banner-slide active">
            <img src="{{ asset('assets/img/banner1.png') }}" alt="Aushvera Banner 1">
        </div>
        <div class="banner-slide">
            <img src="{{ asset('assets/img/banner2.png') }}" alt="Aushvera Banner 2">
        </div>
        <div class="banner-slide">
            <img src="{{ asset('assets/img/banner3.png') }}" alt="Aushvera Banner 3">
        </div>
    </div>
    <div class="hero-content">
        <div class="hero-slide-content active">
            <div class="hero-text">
                <div class="ornamental-divider">
                    <img src="{{ asset('assets/img/pattern.png') }}" alt="" style="width: 500px; height: auto;">
                </div>
                <h1>Discover The<br><span style="color: #B8964C; font-size: 72px; font-family: 'Playfair Display', serif; font-style: italic;">Healing Wisdom</span><br>of Ayurveda</h1>
                <div class="ornamental-divider">
                    <img src="{{ asset('assets/img/pattern.png') }}" alt="" style="width: 500px; height: auto; transform: scaleY(-1);">
                </div>
                <a href="{{ route('products') }}" class="cta-primary" style="margin-top: 2rem;">SHOP NOW</a>
            </div>
        </div>
        <div class="hero-slide-content">
            <div class="hero-text">
                <div class="ornamental-divider">
                    <img src="{{ asset('assets/img/pattern.png') }}" alt="" style="width: 500px; height: auto;">
                </div>
                <h1>Embrace<br><span style="color: #B8964C; font-size: 72px; font-family: 'Playfair Display', serif; font-style: italic;">Ancient Rituals</span><br>for Modern Life</h1>
                <div class="ornamental-divider">
                    <img src="{{ asset('assets/img/pattern.png') }}" alt="" style="width: 500px; height: auto; transform: scaleY(-1);">
                </div>
                <a href="{{ route('ritual') }}" class="cta-primary" style="margin-top: 2rem;">EXPLORE RITUALS</a>
            </div>
        </div>
        <div class="hero-slide-content">
            <div class="hero-text">
                <h1 style="font-family: 'Cinzel', serif; font-size: 60px; color: var(--cream); letter-spacing: 0.05em; line-height: 1.1; margin-bottom: 32px;">Wellness,<br>Refined by Nature.</h1>
                <p style="font-size: 16px; color: rgba(247, 244, 238, 0.75); font-weight: 300; line-height: 1.7; max-width: 550px; margin-bottom: 48px;">Rooted in ancient botanical wisdom, crafted for the modern world.</p>
                <a href="{{ route('ritual') }}" class="cta-primary">Explore the Ritual</a>
            </div>
        </div>
    </div>
</section>

<section class="heritage fade-in">
    <div class="heritage-grid">
        <div class="heritage-text">
            <h2>Honoring Heritage, Refining Wellness.</h2>
            <p>For centuries, botanical rituals have sustained vitality across cultures. AUSHVERA honors this wisdom—not by replicating the past, but by refining it for the present.</p>
            <p>Each formulation is a study in restraint: only what serves, nothing more. Clean ingredients. Timeless presentation. Quiet confidence.</p>
            <p>Improve respiratory health and digestion.</p>
            <a href="{{ route('about') }}" class="btn-outline">Our Story</a>
        </div>
        <div class="heritage-image">
            <img src="{{ asset('assets/img/ritual-image.png') }}" alt="Ritual Scene" style="width: 80%; height: 80%; object-fit: contain;" onerror="this.src='https://images.unsplash.com/photo-1616628188859-7a11abb6fcc9?w=800'">
        </div>
    </div>
</section>

<section class="philosophy-section fade-in">
    <h2>The Aushvera Philosophy</h2>
    <div class="pillars">
        <div class="pillar">
            <div class="pillar-icon">
                <img src="{{ asset('assets/img/purity.png') }}" alt="Purity" style="width: 100px; height: 100px; object-fit: contain;">
            </div>
            <h3>Plant-First Formulation</h3>
            <p>Botanicals chosen for their time-tested efficacy, not trends.</p>
        </div>
        <div class="pillar">
            <div class="pillar-icon">
                <img src="{{ asset('assets/img/legacy.png') }}" alt="Legacy" style="width: 100px; height: 100px; object-fit: contain;">
            </div>
            <h3>Clean-Label Integrity</h3>
            <p>Every ingredient listed. No proprietary blends. Total transparency.</p>
        </div>
        <div class="pillar">
            <div class="pillar-icon">
                <img src="{{ asset('assets/img/precision.png') }}" alt="Precision" style="width: 100px; height: 100px; object-fit: contain;">
            </div>
            <h3>Timeless Presentation</h3>
            <p>Designed to endure beyond seasons, campaigns, and hype.</p>
        </div>
    </div>
</section>

<section class="purity-section fade-in" style="padding: 6rem 3rem; background: var(--cream); text-align: center;">
    <div class="container-narrow" style="max-width: 1000px; margin: 0 auto;">
        <h2 style="font-family: 'Cinzel', serif; font-size: 32px; color: var(--navy-deep); margin-bottom: 24px;">Purity & Sourcing</h2>
        <div class="divider-gold" style="width: 60px; height: 2px; background: var(--gold); margin: 0 auto 32px;"></div>
        <p class="section-intro" style="font-size: 16px; color: var(--charcoal); margin-bottom: 48px;">Every ingredient is traced, tested, and transparently sourced from ethical partners who share our commitment to botanical integrity.</p>
        
        <div class="purity-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 4rem; text-align: left;">
            <div class="purity-item">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--gold); margin-bottom: 12px; font-style: italic;">Water-Based Extraction</h3>
                <p style="font-size: 15px; color: var(--charcoal);">No harsh chemicals. Only pure water extraction to preserve the natural essence of each botanical.</p>
            </div>
            <div class="purity-item">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--gold); margin-bottom: 12px; font-style: italic;">Third-Party Tested</h3>
                <p style="font-size: 15px; color: var(--charcoal);">Every batch undergoes rigorous testing for purity, potency, and safety by independent laboratories.</p>
            </div>
            <div class="purity-item">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--gold); margin-bottom: 12px; font-style: italic;">Sustainable Sourcing</h3>
                <p style="font-size: 15px; color: var(--charcoal);">We partner with regenerative farms that honor the earth and support local communities.</p>
            </div>
            <div class="purity-item">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--gold); margin-bottom: 12px; font-style: italic;">No Artificial Additives</h3>
                <p style="font-size: 15px; color: var(--charcoal);">Zero preservatives, fillers, or synthetic ingredients. Only what nature intended.</p>
            </div>
        </div>
    </div>
</section>

<section class="product-carousel-section fade-in" style="padding: 8rem 3rem; background: var(--beige);">
    <h2 style="font-family: 'Cinzel', serif; font-size: 36px; color: var(--navy-deep); text-align: center; margin-bottom: 48px;">Our Wellness Collection</h2>
    
    @if($products && $products->count() > 0)
    <div class="products-grid" style="max-width: 1200px; margin: 0 auto;">
        @foreach($products->take(4) as $product)
        <a href="{{ route('product') }}?id={{ $product->id }}" class="product-card" style="text-decoration: none; color: inherit; text-align: center; display: block;">
            <div class="product-image" style="background: white; padding: 20px; height: 350px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <img src="{{ $product->image ?? asset('assets/img/product.jpeg') }}" 
                     alt="{{ $product->name }}"
                     onerror="this.src='{{ asset('assets/img/product.jpeg') }}'"
                     style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 24px; color: var(--navy-deep); font-style: italic;">{{ $product->name }}</h3>
            <p style="font-size: 14px; color: var(--charcoal); letter-spacing: 0.1em; text-transform: uppercase;">{{ $product->category ? $product->category->name : 'Wellness' }}</p>
            <span style="display: block; margin-top: 10px; font-family: 'Playfair Display', serif; font-size: 20px; color: var(--gold);">₹{{ number_format($product->price, 2) }}</span>
        </a>
        @endforeach
    </div>
    @else
    <div style="text-align: center; padding: 40px;">
        <p>No products available yet.</p>
    </div>
    @endif
    
    <div style="text-align: center; margin-top: 60px;">
        <a href="{{ route('products') }}" class="btn-outline">View All Products</a>
    </div>
</section>

<section class="product-showcase-section fade-in">
    <div class="product-showcase-grid">
        <div class="product-text">
            <h2>Ashvattha Classic</h2>
            <p class="product-tagline">A Sacred Leaf. A Modern Ritual.</p>
            <ul class="product-benefits">
                <li>Naturally calming</li>
                <li>Traditionally respected</li>
                <li>Designed for daily rituals</li>
            </ul>
            <a href="{{ route('products') }}" class="cta-primary">Discover More</a>
        </div>
        <div class="product-image-container">
            <img src="{{ asset('assets/img/Ashvattha Classic.jpeg') }}" alt="Ashvattha Classic" class="showcase-product-image" onerror="this.src='{{ asset('assets/img/product.jpeg') }}'">
        </div>
    </div>
</section>

<section class="ritual-section fade-in" id="ritual">
    <h2>Your Daily Ritual</h2>
    <div class="ritual-steps-grid">
        <div class="ritual-step">
            <div class="ritual-number">1</div>
            <h3>Morning Intention</h3>
            <p>Begin each day with a moment of stillness and a cup of Ashvattha™.</p>
        </div>
        <div class="ritual-step">
            <div class="ritual-number">2</div>
            <h3>Mindful Preparation</h3>
            <p>Steep with care. Let the ritual slow you down, even for a moment.</p>
        </div>
        <div class="ritual-step">
            <div class="ritual-number">3</div>
            <h3>Consistent Practice</h3>
            <p>Wellness unfolds over time. Commit to the practice, not the promise.</p>
        </div>
    </div>
    <div class="ritual-cta" style="margin-top: 3rem;">
        <a href="{{ route('ritual') }}" class="btn-outline">Discover Ashvattha™</a>
    </div>
</section>

@endsection

@push('scripts')
<script>
// Banner slider
let currentSlide = 0;
const slides = document.querySelectorAll('.banner-slide');
const textSlides = document.querySelectorAll('.hero-slide-content');

function nextSlide() {
    slides[currentSlide].classList.remove('active');
    if (textSlides[currentSlide]) textSlides[currentSlide].classList.remove('active');
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add('active');
    if (textSlides[currentSlide]) textSlides[currentSlide].classList.add('active');
}

if (slides.length > 1) {
    setInterval(nextSlide, 5000);
}

// Scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
</script>
@endpush

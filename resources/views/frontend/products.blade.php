@extends('layouts.app')

@section('title', 'Products — AUSHVERA')
@section('description', 'Explore our full collection of premium Ayurvedic wellness products.')

@section('content')
<section style="padding: 150px 3rem 100px; background: var(--cream); min-height: 100vh;">
    <div class="container">
        <h1 style="text-align: center; font-size: 48px; margin-bottom: 60px; color: var(--navy-deep); font-family: 'Playfair Display', serif;">Our Products</h1>

        {{-- Grid --}}
        @if($products && $products->count() > 0)
        <div class="products-grid" id="productsGrid" style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 60px;">
            @foreach($products as $product)
            <div class="product-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.3s;"
                 onmouseover="this.style.transform='translateY(-5px)'"
                 onmouseout="this.style.transform='translateY(0)'"
                 onclick="window.location.href='{{ route('product') }}?id={{ $product->id }}'"
                 data-cat="{{ $product->category_id ?? 'none' }}"
                 data-price="{{ $product->price }}"
                 data-name="{{ strtolower($product->name) }}">
                <div style="text-decoration: none; color: inherit; display: block;">
                    <div class="product-card-img" style="width: 100%; height: 250px; background: white; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <img src="{{ $product->image ?? asset('assets/img/product.jpeg') }}" 
                             alt="{{ $product->name }}"
                             loading="lazy"
                             onerror="this.src='{{ asset('assets/img/product.jpeg') }}'"
                             style="width: 100%; height: 100%; object-fit: cover;">
                        @if(($product->stock ?? 10) < 5 && ($product->stock ?? 10) > 0)
                            <span class="product-badge product-badge--warn" style="position: absolute; top: 10px; right: 10px; background: #f59e0b; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Low Stock</span>
                        @elseif(($product->stock ?? 10) == 0)
                            <span class="product-badge product-badge--out" style="position: absolute; top: 10px; right: 10px; background: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Out of Stock</span>
                        @endif
                    </div>
                    <div class="product-card-body" style="padding: 20px;">
                        @if($product->category)
                            <p class="product-category" style="font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">{{ $product->category->name }}</p>
                        @endif
                        <h3 class="product-name" style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--navy-deep); margin-bottom: 8px;">{{ $product->name }}</h3>
                        <p class="product-desc" style="font-size: 14px; color: var(--charcoal); margin-bottom: 12px; height: 40px; overflow: hidden;">{{ Str::limit($product->description, 80) }}</p>
                        <div class="product-footer" style="display: flex; align-items: center; gap: 10px;">
                            @if($product->original_price)
                                <span style="font-size: 16px; color: #999; text-decoration: line-through;">₹{{ number_format($product->original_price, 2) }}</span>
                            @endif
                            <span class="product-price" style="font-weight: bold; font-size: 24px; color: var(--gold); font-weight: bold;">₹{{ number_format($product->price, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="no-results" id="noResults" style="display:none;">
            <p>No products matched your search.</p>
        </div>
        @else
        <div class="empty-state">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <h3>No products yet</h3>
            <p>Products will appear here once added by the admin.</p>
        </div>
        @endif

        {{-- Pagination --}}
        @if($products && $products->hasPages())
        <div class="pagination-container" style="display: flex; justify-content: center; margin-top: 60px; margin-bottom: 40px;">
            {{ $products->links() }}
        </div>
        @endif

    </div>
</section>

<div class="toast" id="toast"></div>
@endsection

@push('scripts')
<script>
// Reveal animation
const grid = document.getElementById('productsGrid');
const cards = grid ? Array.from(grid.querySelectorAll('.product-card')) : [];

const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { 
        if (e.isIntersecting) {
            e.target.style.opacity = 1;
            e.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.05 });

cards.forEach(c => {
    c.style.opacity = 0;
    c.style.transform = 'translateY(20px)';
    c.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(c);
});
</script>
@endpush

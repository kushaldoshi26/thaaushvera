@extends('layouts.app')

@section('title', isset($product) ? $product->name . ' — AUSHVERA' : 'Product — AUSHVERA')
@section('description', isset($product) ? Str::limit($product->description, 150) : 'Premium Ayurvedic wellness product.')

@push('styles')
<link rel="stylesheet" href="{{ asset('styles.css') }}">
<style>
/* Scoped inline fixes for legacy product CSS */
.product-hero { padding-top: 150px; background-color: var(--cream); }
.product-container { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; padding: 0 3rem; }
.product-gallery { position: sticky; top: 100px; }
.main-product-image { border-radius: 12px; overflow: hidden; background: white; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
.main-product-image img { width: 100%; height: 500px; object-fit: contain; }
.thumbnail-gallery { display: flex; gap: 15px; }
.thumbnail { width: 80px; height: 80px; border-radius: 8px; cursor: pointer; border: 2px solid transparent; object-fit: contain; background: white; }
.thumbnail.active { border-color: var(--gold); }
.product-title { font-family: 'Playfair Display', serif; font-size: 42px; color: var(--navy-deep); margin-bottom: 10px; }
.product-rating { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
.stars { color: #C6A75E; letter-spacing: 2px; }
.rating-text { color: var(--charcoal); font-size: 14px; }
.product-subtitle { font-family: 'Playfair Display', serif; font-style: italic; font-size: 20px; color: var(--gold); margin-bottom: 25px; }
.title-divider { width: 60px; height: 2px; background: var(--gold); margin-bottom: 25px; }
.product-description { font-size: 16px; color: var(--charcoal); line-height: 1.8; margin-bottom: 30px; }
.price-section { display: flex; align-items: baseline; gap: 15px; margin-bottom: 30px; }
.current-price { font-family: 'Playfair Display', serif; font-size: 36px; color: var(--navy-deep); }
.original-price { font-size: 20px; color: #999; text-decoration: line-through; }
.savings { background: #E8F5E9; color: #2E7D32; padding: 4px 10px; border-radius: 4px; font-size: 14px; font-weight: bold; }
.purchase-options { background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid rgba(198, 167, 94, 0.3); }
.radio-option { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; cursor: pointer; }
.radio-option span { font-size: 16px; color: var(--navy-deep); }
.subscribe-price { margin-left: auto; font-family: 'Playfair Display', serif; font-size: 18px; color: var(--gold); font-weight: bold; }
.delivery-frequency { margin-left: 25px; margin-bottom: 15px; display: flex; align-items: center; gap: 15px; }
.delivery-frequency select { padding: 8px; border-radius: 4px; border: 1px solid #ddd; }
.quantity-section { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; }
.quantity-selector { display: flex; align-items: center; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; }
.qty-btn { background: #f9f9f9; border: none; padding: 10px 15px; cursor: pointer; font-size: 18px; }
.qty-btn:hover { background: #eee; }
.quantity-selector input { width: 50px; text-align: center; border: none; font-size: 16px; }
.bundle-offers { display: flex; gap: 15px; margin-bottom: 30px; }
.offer-tag { background: #FFF4E5; color: #D97706; padding: 8px 15px; border-radius: 20px; font-size: 14px; font-weight: bold; border: 1px dashed #D97706; }
.action-buttons { display: flex; gap: 15px; margin-bottom: 15px; }
.buy-now-btn { flex: 1; padding: 15px; background: var(--gold); color: white; border: none; border-radius: 4px; font-family: 'Cinzel', serif; font-size: 16px; cursor: pointer; transition: background 0.3s; }
.buy-now-btn:hover { background: #a68a4a; }
.wishlist-btn { width: 52px; display: flex; align-items: center; justify-content: center; background: white; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; color: var(--charcoal); transition: all 0.3s; }
.wishlist-btn:hover { border-color: var(--gold); color: var(--gold); }
.wishlist-btn.active svg { fill: var(--gold); stroke: var(--gold); }
.add-to-cart-btn-full { width: 100%; padding: 15px; background: var(--navy-deep); color: white; border: none; border-radius: 4px; font-family: 'Cinzel', serif; font-size: 16px; cursor: pointer; transition: background 0.3s; margin-bottom: 30px; }
.add-to-cart-btn-full:hover { background: #071421; }
.delivery-info { background: #F8F9FA; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
.delivery-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: var(--charcoal); font-size: 14px; }
.delivery-item:last-child { margin-bottom: 0; }
.trust-signals { display: flex; flex-wrap: wrap; gap: 15px; }
.trust-item { font-size: 13px; color: #666; display: flex; align-items: center; gap: 5px; }
</style>
@endpush

@section('content')

@if(isset($product))
<section class="product-hero">
    <div class="product-container">
        <div class="product-image-section">
            <div class="product-gallery">
                <div class="main-product-image">
                    <img src="{{ $product->image ?? asset('assets/img/product.jpeg') }}" alt="{{ $product->name }}" class="active-image" id="mainImage" onerror="this.src='{{ asset('assets/img/product.jpeg') }}'">
                </div>
            </div>
        </div>

        <div class="product-info-section">
            <h1 class="product-title">{{ $product->name }}</h1>
            
            <div class="product-rating">
                <div class="stars">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                </div>
                <span class="rating-text">4.8 (1,248 reviews)</span>
            </div>
            
            <p class="product-subtitle">{{ $product->category ? $product->category->name : 'Premium Ayurvedic Formulation' }}</p>
            
            <div class="title-divider"></div>
            
            <p class="product-description">
                {{ $product->description }}
            </p>

            <div class="price-section">
                <div class="current-price">₹{{ number_format($product->price, 2) }}</div>
                @if($product->original_price && $product->original_price > $product->price)
                <div class="original-price">₹{{ number_format($product->original_price, 2) }}</div>
                <div class="savings">Save ₹{{ number_format($product->original_price - $product->price, 0) }}</div>
                @endif
            </div>

            <div class="purchase-options">
                <div class="purchase-type">
                    <label class="radio-option">
                        <input type="radio" name="purchase-type" value="onetime" checked>
                        <span>One-time purchase</span>
                    </label>
                </div>
            </div>

            <div class="quantity-section">
                <label>Quantity:</label>
                <div class="quantity-selector">
                    <button class="qty-btn" onclick="decreaseQty()">−</button>
                    <input type="number" id="quantity" value="1" min="1" readonly>
                    <button class="qty-btn" onclick="increaseQty()">+</button>
                </div>
            </div>

            <div class="action-buttons">
                <button class="buy-now-btn" onclick="buyNow()">ADD TO CART</button>
            </div>
            <button class="add-to-cart-btn-full" onclick="buyNow()" style="background: var(--navy-deep);">BUY NOW</button>

            <div class="delivery-info">
                <div class="delivery-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="3" width="15" height="13"/>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                        <circle cx="5.5" cy="18.5" r="2.5"/>
                        <circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                    <span>Free Shipping across India</span>
                </div>
                <div class="delivery-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>Delivery in 2–4 business days</span>
                </div>
            </div>

            <div class="trust-signals">
                <div class="trust-item">✓ Secure Checkout</div>
                <div class="trust-item">✓ 100% Authentic</div>
                <div class="trust-item">✓ Lab Tested</div>
                <div class="trust-item">✓ COD Available</div>
            </div>
        </div>
    </div>
</section>

@else
<div class="page-hero page-hero--short" style="padding-top: 15rem;">
    <div class="container" style="text-align:center;">
        <h1>Product Not Found</h1>
        <p style="margin:1rem 0;">The product you're looking for doesn't exist.</p>
        <a href="{{ route('products') }}" class="btn btn--primary">Browse All Products</a>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    function increaseQty() {
        const qtyInput = document.getElementById('quantity');
        qtyInput.value = parseInt(qtyInput.value) + 1;
    }

    function decreaseQty() {
        const qtyInput = document.getElementById('quantity');
        if (parseInt(qtyInput.value) > 1) {
            qtyInput.value = parseInt(qtyInput.value) - 1;
        }
    }

    function buyNow() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const quantity = parseInt(document.getElementById('quantity').value);
        const productId = '{{ $product->id ?? '' }}';
        
        if(!productId) return;
        
        const existing = cart.find(i => i.id === productId);
        if (existing) {
            existing.quantity = (existing.quantity || 1) + quantity;
        } else {
            cart.push({
                id: productId,
                name: '{{ addslashes($product->name ?? '') }}',
                price: parseFloat('{{ $product->price ?? 0 }}'),
                image: '{{ $product->image ?? asset("assets/img/product.jpeg") }}',
                quantity: quantity
            });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        window.dispatchEvent(new Event('cart-updated'));
        window.location.href = "{{ route('cart') }}";
    }
</script>
@endpush

@extends('layouts.app')

@section('title', 'Shopping Cart — AUSHVERA')

@push('styles')
<style>
    .cart-page {
        min-height: 100vh;
        padding: 10rem 3rem 6rem;
        background: var(--cream);
    }
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .cart-title {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        color: var(--navy-deep);
        margin-bottom: 3rem;
        text-align: center;
    }
    .cart-empty {
        text-align: center;
        padding: 4rem 2rem;
    }
    .cart-empty p {
        font-size: 1.2rem;
        color: var(--navy-deep);
        opacity: 0.7;
        margin-bottom: 2rem;
    }
    .cart-empty .btn-outline {
        display: inline-block;
        padding: 12px 30px;
        border: 1px solid var(--gold);
        color: var(--navy-deep);
        text-decoration: none;
        transition: all 0.3s;
    }
    .cart-empty .btn-outline:hover {
        background: var(--gold);
        color: white;
    }
    .cart-items {
        background: white;
        border: 1px solid rgba(198, 167, 94, 0.2);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .cart-item-row {
        display: grid;
        grid-template-columns: 100px 1fr 150px 150px 100px;
        gap: 2rem;
        align-items: center;
        padding: 2rem 0;
        border-bottom: 1px solid rgba(198, 167, 94, 0.1);
    }
    .cart-item-row:last-child {
        border-bottom: none;
    }
    .cart-item-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 1px solid rgba(198, 167, 94, 0.2);
    }
    .cart-item-info h3 {
        font-size: 1.2rem;
        color: var(--navy-deep);
        margin-bottom: 0.5rem;
    }
    .cart-item-info p {
        color: var(--gold);
        font-size: 0.9rem;
    }
    .cart-qty {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .cart-qty button {
        width: 32px;
        height: 32px;
        border: 1px solid var(--gold);
        background: transparent;
        color: var(--gold);
        cursor: pointer;
        transition: all 0.3s;
    }
    .cart-qty button:hover {
        background: var(--gold);
        color: var(--navy-deep);
    }
    .cart-qty span {
        color: var(--navy-deep);
        font-weight: 500;
    }
    .cart-price {
        font-size: 1.2rem;
        color: var(--navy-deep);
        font-weight: 500;
    }
    .cart-remove {
        background: none;
        border: none;
        color: var(--navy-deep);
        opacity: 0.5;
        cursor: pointer;
        font-size: 1.5rem;
        transition: opacity 0.3s;
    }
    .cart-remove:hover {
        opacity: 1;
    }
    .cart-summary {
        background: white; /* fallback beige is defined in styles usually but white looks cleaner */
        padding: 2rem;
        border: 1px solid rgba(198, 167, 94, 0.2);
    }
    .cart-summary-row {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(198, 167, 94, 0.1);
    }
    .cart-summary-row:last-child {
        border-bottom: none;
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--navy-deep);
    }
    .cart-actions {
        display: flex;
        gap: 2rem;
        margin-top: 2rem;
    }
    .cart-actions .btn-outline {
        flex: 1;
        padding: 15px;
        text-align: center;
        border: 1px solid var(--gold);
        color: var(--navy-deep);
        text-decoration: none;
        transition: all 0.3s;
    }
    .cart-actions .btn-outline:hover {
        background: var(--gold);
        color: white;
    }
    .cart-actions .btn-gold {
        flex: 1;
        padding: 15px;
        background: var(--gold);
        color: white;
        border: none;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s;
    }
    .cart-actions .btn-gold:hover {
        background: #a68a4a;
    }
    @media (max-width: 768px) {
        .cart-item-row {
            grid-template-columns: 80px 1fr;
            grid-template-rows: auto auto auto;
            gap: 1rem;
        }
        .cart-item-img {
            width: 80px;
            height: 80px;
            grid-row: span 3;
        }
        .cart-remove {
            justify-self: end;
        }
        .cart-price, .cart-qty {
            justify-self: start;
        }
        .cart-actions {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<section class="cart-page">
    <div class="cart-container">
        <h1 class="cart-title">Shopping Cart</h1>
        <div id="cart-content">
            <!-- Cart dynamically loaded here by script -->
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const productsRoute = "{{ route('products') }}";
    const paymentRoute = "{{ route('payment') }}";
    
    // Load cart from localStorage
    function renderCart() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartContent = document.getElementById('cart-content');
        
        if (cart.length === 0) {
            cartContent.innerHTML = '<div class="cart-empty"><p>Your cart is empty</p><a href="' + productsRoute + '" class="btn-outline">Shop Now</a></div>';
            return;
        }
        
        let itemsHTML = '<div class="cart-items">';
        let subtotal = 0;
        
        cart.forEach((item, index) => {
            const itemTotal = item.price * (item.quantity || 1);
            subtotal += itemTotal;
            const imgPath = item.image ? item.image : "{{ asset('assets/img/product.jpeg') }}";
            
            itemsHTML += `
                <div class="cart-item-row">
                    <img src="${imgPath}" alt="${item.name}" class="cart-item-img" onerror="this.src='{{ asset('assets/img/product.jpeg') }}'">
                    <div class="cart-item-info">
                        <h3>${item.name}</h3>
                        <p>${item.description || 'Premium Product'}</p>
                    </div>
                    <div class="cart-qty">
                        <button onclick="updateQuantity(${index}, -1)">−</button>
                        <span>${item.quantity || 1}</span>
                        <button onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                    <div class="cart-price">₹${itemTotal.toFixed(2)}</div>
                    <button class="cart-remove" onclick="removeItem(${index})">×</button>
                </div>
            `;
        });
        
        itemsHTML += '</div>';
        
        let discountPct = window.appliedDiscountPct || 0;
        let discountStr = '';
        let discountDisplay = 'none';
        let discountAmount = 0;
        
        if (discountPct > 0) {
            discountAmount = subtotal * (discountPct / 100);
            discountDisplay = 'flex';
            discountStr = window.appliedDiscountCode || '';
        }
        
        let total = Math.max(0, subtotal - discountAmount);
        
        itemsHTML += `
            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">₹${subtotal.toFixed(2)}</span>
                </div>
                <div class="cart-summary-row" style="border-bottom: 1px solid rgba(198, 167, 94, 0.1);">
                    <div style="display: flex; gap: 10px; width: 100%;">
                        <input type="text" id="couponCode" placeholder="Enter coupon code" style="flex: 1; padding: 10px; border: 1px solid rgba(198, 167, 94, 0.3);">
                        <button onclick="applyCoupon()" style="padding: 10px 20px; background: var(--gold); color: white; border: none; cursor: pointer;">Apply</button>
                    </div>
                </div>
                <div class="cart-summary-row" id="discountRow" style="display: ${discountDisplay}; color: green;">
                    <span>Discount (<span id="discountLabel">${discountStr}</span>)</span>
                    <span id="discountAmount">-₹${discountAmount.toFixed(2)}</span>
                </div>
                <div class="cart-summary-row">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <div class="cart-summary-row">
                    <span>Total</span>
                    <span id="totalAmount">₹${total.toFixed(2)}</span>
                </div>
            </div>
            <div class="cart-actions">
                <a href="${productsRoute}" class="btn-outline">Continue Shopping</a>
                <button class="btn-gold" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        `;
        
        cartContent.innerHTML = itemsHTML;
        
        localStorage.setItem('orderSubtotal', subtotal.toFixed(2));
        localStorage.setItem('orderTotal', total.toFixed(2));
        localStorage.setItem('orderShipping', 0);
    }
    
    function updateQuantity(index, change) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart[index].quantity = (cart[index].quantity || 1) + change;
        if (cart[index].quantity < 1) {
            removeItem(index);
            return;
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        window.dispatchEvent(new Event('cart-updated'));
        renderCart();
    }
    
    function removeItem(index) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        window.dispatchEvent(new Event('cart-updated'));
        renderCart();
    }
    
    function proceedToCheckout() {
        if(window.location.href.includes('127.0.0.1')) {
            window.location.href = paymentRoute;
        } else {
             // Let user try default path, could be relative
             window.location.href = paymentRoute;
        }
    }

    async function applyCoupon() {
        const code = document.getElementById('couponCode').value.toUpperCase().trim();
        if (!code) {
            alert('Please enter a coupon code');
            return;
        }

        try {
            const response = await fetch('/api/coupons/validate', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ code: code })
            });

            const data = await response.json();

            if (data.valid) {
                window.appliedDiscountPct = data.discount_percent || 0;
                window.appliedDiscountCode = code;
                renderCart();
                alert('Coupon applied successfully! ' + window.appliedDiscountPct + '% off.');
            } else {
                alert(data.message || 'Invalid coupon code');
            }
        } catch (error) {
            // Offline fallback or error
            if(code === '10OFF') {
                window.appliedDiscountPct = 10;
                window.appliedDiscountCode = code;
                renderCart();
                alert('Coupon applied successfully! 10% off.');
            } else {
                alert('Invalid coupon code');
            }
        }
    }

    // Initial render
    renderCart();
</script>
@endpush

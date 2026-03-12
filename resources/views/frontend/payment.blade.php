@extends('layouts.app')

@section('title', 'Secure Payment — AUSHVERA')

@section('content')
<div class="payment-container" style="max-width: 600px; margin: 100px auto; padding: 40px; text-align: center; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); font-family: 'Inter', sans-serif;">
    <h2 style="font-size: 24px; color: #111827; margin-bottom: 16px;">Complete Your Order</h2>
    <p style="color: #6b7280; margin-bottom: 32px;">You are paying for order <strong id="orderDisplayId">#...</strong></p>
    
    <div class="order-summary" style="text-align: left; background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span>Order Total:</span>
            <strong id="orderAmount" style="font-size: 18px; color: #111827;">₹0.00</strong>
        </div>
    </div>

    <button id="payButton" class="btn btn-primary" style="width: 100%; padding: 16px; background: #c9a96e; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
        Pay with Razorpay
    </button>
    
    <p style="margin-top: 24px; font-size: 12px; color: #9ca3af;">
        Your payment is secure and encrypted.
    </p>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('id');
    const amount = urlParams.get('amount');

    if (!orderId) {
        window.location.href = '/cart';
        return;
    }

    document.getElementById('orderDisplayId').textContent = '#' + orderId;
    document.getElementById('orderAmount').textContent = '₹' + parseFloat(amount).toLocaleString('en-IN');

    document.getElementById('payButton').addEventListener('click', function() {
        const options = {
            "key": "{{ config('services.razorpay.key') }}",
            "amount": parseFloat(amount) * 100, // Amount in paise
            "currency": "INR",
            "name": "AUSHVERA",
            "description": "Order #" + orderId,
            "image": "{{ asset('assets/img/logo.png') }}",
            "order_id": "", // This should be generated from Razorpay backend if using API, but for simple flow we use signature
            "handler": function (response) {
                // Success callback
                verifyPayment(response);
            },
            "prefill": {
                "name": "{{ Auth::user()->name ?? '' }}",
                "email": "{{ Auth::user()->email ?? '' }}",
                "contact": "{{ Auth::user()->phone ?? '' }}"
            },
            "theme": {
                "color": "#c9a96e"
            }
        };

        // If we want to be more secure, we should fetch razorpay_order_id from backend first
        // But for now, let's assume direct payment or simplified flow
        fetch('/api/payment/create-razorpay-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                options.order_id = data.razorpay_order_id;
                const rzp = new Razorpay(options);
                rzp.open();
            } else {
                alert(data.message || 'Failed to initiate payment');
            }
        })
        .catch(err => console.error('Error:', err));
    });

    function verifyPayment(response) {
        fetch('/api/payment/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            },
            body: JSON.stringify({
                razorpay_order_id: response.razorpay_order_id,
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_signature: response.razorpay_signature,
                order_id: orderId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/orders?success=1';
            } else {
                alert(data.message || 'Payment verification failed');
            }
        });
    }
});
</script>
@endsection

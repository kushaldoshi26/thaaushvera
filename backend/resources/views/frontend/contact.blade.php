@extends('layouts.app')

@section('title', 'Contact Us — AUSHVERA')

@push('styles')
<style>
    .contact-container {
        max-width: 1200px;
        margin: 5rem auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        color: var(--text-color);
    }
    
    @media (max-width: 768px) {
        .contact-container {
            grid-template-columns: 1fr;
            gap: 3rem;
            margin: 3rem auto;
        }
    }

    .contact-info h2 {
        font-family: var(--font-serif);
        font-size: 3rem;
        color: #C6A75E; /* Brand Gold */
        margin-bottom: 1.5rem;
    }

    .contact-info p {
        color: var(--text-muted);
        line-height: 1.7;
        margin-bottom: 2.5rem;
        font-size: 1.15rem;
    }

    .contact-details {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .contact-detail-item {
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        padding: 1rem;
        border-radius: 12px;
        background: transparent;
        border-left: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .contact-detail-item:hover {
        border-left: 2px solid #C6A75E;
        background: rgba(204, 169, 104, 0.05);
    }

    .contact-icon {
        color: #ffffff;
        background: #C6A75E;
        padding: 1rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(204, 169, 104, 0.3);
    }

    .contact-detail-item h4 {
        margin: 0 0 0.5rem 0;
        color: var(--text-color);
        font-family: var(--font-serif);
        font-size: 1.3rem;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .contact-detail-item p {
        margin: 0;
        font-size: 1.05rem;
        color: #4b5563; /* Dark gray for excellent contrast on light backgrounds */
        line-height: 1.6;
    }

    .contact-form-box {
        background: #ffffff;
        padding: 3rem;
        border-radius: 16px;
        border: 1px solid rgba(204, 169, 104, 0.4);
        box-shadow: 0 15px 40px rgba(204, 169, 104, 0.15);
        position: relative;
    }
    
    .contact-form-box::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 5px;
        background: #C6A75E;
    }

    .contact-form-box h3 {
        font-family: var(--font-serif);
        font-size: 2.2rem;
        color: #0B1C2D;
        margin-bottom: 2rem;
        text-align: center;
    }

    .contact-form .form-group {
        margin-bottom: 1.5rem;
    }

    .contact-form .form-control {
        width: 100%;
        padding: 1.25rem;
        background: #FCFAF6;
        border: 1px solid rgba(204, 169, 104, 0.4);
        border-radius: 8px;
        color: #0B1C2D;
        font-family: var(--font-sans);
        transition: all 0.3s ease;
        box-sizing: border-box;
        font-size: 1.05rem;
    }

    .contact-form .form-control:focus {
        outline: none;
        border-color: #C6A75E;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(204, 169, 104, 0.15);
    }
    
    .contact-form .form-control::placeholder {
        color: #9ca3af;
    }

    .contact-form textarea.form-control {
        resize: vertical;
        min-height: 160px;
    }

    .contact-form .btn-submit {
        width: 100%;
        padding: 1.25rem;
        background: #C6A75E;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-family: var(--font-sans);
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        margin-top: 1rem;
        box-shadow: 0 4px 15px rgba(204, 169, 104, 0.4);
    }

    .contact-form .btn-submit:hover {
        background: #0B1C2D;
        color: #C6A75E;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(11, 28, 45, 0.4);
    }

    .alert-success {
        background: rgba(5, 150, 105, 0.1);
        border: 1px solid rgba(5, 150, 105, 0.3);
        color: #10b981;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        text-align: center;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<!-- Re-use the existing page-hero utility class from global styles -->
<div class="page-hero">
    <div class="page-hero-bg">
        <img src="{{ asset('assets/img/hero-bg.jpeg') }}" alt="Contact Aushvera" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="page-hero-overlay"></div>
    <div class="page-hero-content">
        <h1 class="page-hero-title">Contact Us</h1>
        <p class="page-hero-subtitle" style="max-width: 600px; margin: 0 auto;">We would love to hear from you. Reach out for any inquiries about our Ayurvedic collections.</p>
    </div>
</div>

<section class="contact-section">
    <div class="contact-container">
        <!-- Contact Info -->
        <div class="contact-info">
            <h2>Get in Touch</h2>
            <p>Our dedicated team is here to assist you with any questions regarding our Ayurvedic products, your recent orders, or general inquiries. We strive to provide the most pure and premium experience.</p>
            
            <div class="contact-details">
                <div class="contact-detail-item">
                    <div class="contact-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div>
                        <h4>Phone</h4>
                        <p>+91 98765 43210</p>
                    </div>
                </div>

                <div class="contact-detail-item">
                    <div class="contact-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <div>
                        <h4>Email</h4>
                        <p>support@aushvera.com</p>
                    </div>
                </div>

                <div class="contact-detail-item">
                    <div class="contact-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <div>
                        <h4>Location</h4>
                        <p>123 Ayurveda Marg, Health Dist<br>Surat, Gujarat 395007</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-box">
            <h3>Send a Message</h3>
            @if(session('contact_success'))
                <div class="alert-success">{{ session('contact_success') }}</div>
            @endif
            
            <form class="contact-form" method="POST" action="{{ route('contact.post') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Your Full Name" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Your Email Address" required value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <textarea name="message" class="form-control" placeholder="How can we help you today?" required>{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>
    </div>
</section>
@endsection

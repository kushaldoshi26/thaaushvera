@extends('layouts.app')

@section('title', 'Server Error — AUSHVERA')
@section('description', 'We are experiencing technical difficulties. Please try again later.')

@section('content')
<section style="padding: 160px 2rem 100px; background: var(--cream); min-height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
    <!-- Decorative background elements -->
    <div style="position: absolute; top: 10%; right: 5%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(184, 150, 76, 0.05) 0%, transparent 70%); filter: blur(60px); z-index: 0;"></div>
    <div style="position: absolute; bottom: 10%; left: 5%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(11, 28, 45, 0.03) 0%, transparent 70%); filter: blur(80px); z-index: 0;"></div>

    <div class="container" style="max-width: 800px; position: relative; z-index: 1; text-align: center;">
        <div style="margin-bottom: 3rem; animation: slideDown 1s ease-out;">
            <h1 style="font-size: clamp(100px, 15vw, 180px); line-height: 1; font-weight: 600; color: var(--gold); margin-bottom: 0; font-family: 'Cinzel', serif; letter-spacing: -5px; opacity: 0.9;">
                500
            </h1>
            <div style="width: 100px; height: 1px; background: var(--gold); margin: 1rem auto; opacity: 0.5;"></div>
        </div>

        <h2 style="font-size: 2.5rem; color: var(--navy-deep); margin-bottom: 1.5rem; font-family: 'Playfair Display', serif; font-weight: 500;">
            A Global Resonance Issue
        </h2>

        <p style="font-size: 1.15rem; color: var(--charcoal); margin-bottom: 3rem; line-height: 1.8; max-width: 550px; margin-left: auto; margin-right: auto; font-family: 'Inter', sans-serif; opacity: 0.8;">
            Something unexpected occurred while processing your request. Our master architects have been notified and are restoring the balance.
        </p>

        <div style="display: flex; gap: 24px; justify-content: center; flex-wrap: wrap; animation: fadeIn 1.5s ease;">
            <a href="{{ route('home') }}" class="cta-primary" style="padding: 18px 45px; min-width: 200px; display: inline-block;">
                Return Home
            </a>

            <button onclick="window.location.reload()" class="cta-secondary" style="padding: 16px 45px; min-width: 200px; display: inline-block; cursor: pointer; background: transparent;">
                Try Again
            </button>
        </div>

        <div style="margin-top: 5rem; padding: 2rem; border-top: 1px solid rgba(184, 150, 76, 0.15); display: inline-block;">
             <p style="font-size: 0.9rem; color: var(--gold); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 0.5rem;">Need Assistance?</p>
             <a href="{{ route('contact') }}" style="color: var(--navy-deep); text-decoration: none; font-weight: 600; border-bottom: 1px solid var(--navy-deep); padding-bottom: 2px; transition: opacity 0.3s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">Contact Concierge</a>
        </div>
    </div>
</section>

<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endsection
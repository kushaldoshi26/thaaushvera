@extends('layouts.app')

@section('title', 'Page Not Found — AUSHVERA')
@section('description', 'Sorry, the page you are looking for does not exist.')

@section('content')
<section style="padding: 160px 2rem 100px; background: var(--cream); min-height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
    <!-- Decorative background elements -->
    <div style="position: absolute; top: 10%; left: 5%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(184, 150, 76, 0.05) 0%, transparent 70%); filter: blur(60px); z-index: 0;"></div>
    <div style="position: absolute; bottom: 10%; right: 5%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(11, 28, 45, 0.03) 0%, transparent 70%); filter: blur(80px); z-index: 0;"></div>

    <div class="container" style="max-width: 800px; position: relative; z-index: 1; text-align: center;">
        <div style="margin-bottom: 3rem; animation: slideDown 1s ease-out;">
            <h1 style="font-size: clamp(100px, 15vw, 180px); line-height: 1; font-weight: 600; color: var(--gold); margin-bottom: 0; font-family: 'Cinzel', serif; letter-spacing: -5px; opacity: 0.9;">
                404
            </h1>
            <div style="width: 100px; height: 1px; background: var(--gold); margin: 1rem auto; opacity: 0.5;"></div>
        </div>

        <h2 style="font-size: 2.5rem; color: var(--navy-deep); margin-bottom: 1.5rem; font-family: 'Playfair Display', serif; font-weight: 500;">
            Lost in the Essence
        </h2>

        <p style="font-size: 1.15rem; color: var(--charcoal); margin-bottom: 3.5rem; line-height: 1.8; max-width: 500px; margin-left: auto; margin-right: auto; font-family: 'Inter', sans-serif; opacity: 0.8;">
            The path you followed seems to have vanished. Let us guide you back to the purity of Aushvera.
        </p>

        <div style="display: flex; gap: 24px; justify-content: center; flex-wrap: wrap; animation: fadeIn 1.5s ease;">
            <a href="{{ route('home') }}" class="cta-primary" style="padding: 18px 45px; min-width: 200px; display: inline-block;">
                Return Home
            </a>

            <a href="{{ route('products') }}" class="cta-secondary" style="padding: 16px 45px; min-width: 200px; display: inline-block;">
                Discover Collections
            </a>
        </div>

        <div style="margin-top: 5rem;">
            <img src="{{ asset('assets/img/pattern.png') }}" alt="" style="width: 80px; height: auto; opacity: 0.2; filter: grayscale(1);">
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
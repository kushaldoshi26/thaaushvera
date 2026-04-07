@extends('layouts.app')

@section('title', 'Page Not Found — AUSHVERA')
@section('description', 'Sorry, the page you are looking for does not exist.')

@section('content')
<section style="padding: 200px 3rem 100px; background: var(--cream); min-height: 100vh; text-align: center;">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 40px;">
            <img src="{{ asset('assets/img/pattern.png') }}" alt="" style="width: 100px; height: auto; opacity: 0.3;">
        </div>

        <h1 style="font-size: 120px; font-weight: bold; color: var(--gold); margin-bottom: 20px; font-family: 'Cinzel', serif;">404</h1>

        <h2 style="font-size: 36px; color: var(--navy-deep); margin-bottom: 20px; font-family: 'Playfair Display', serif;">
            Page Not Found
        </h2>

        <p style="font-size: 18px; color: var(--charcoal); margin-bottom: 40px; line-height: 1.6;">
            Sorry, the page you're looking for doesn't exist or has been moved.
        </p>

        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('home') }}" class="cta-primary" style="padding: 15px 30px; background: var(--gold); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background 0.3s;">
                Go Home
            </a>

            <a href="{{ route('products') }}" class="cta-secondary" style="padding: 15px 30px; background: transparent; color: var(--navy-deep); text-decoration: none; border: 2px solid var(--navy-deep); border-radius: 8px; font-weight: bold; transition: all 0.3s;">
                Browse Products
            </a>

            <a href="{{ route('contact') }}" class="cta-secondary" style="padding: 15px 30px; background: transparent; color: var(--navy-deep); text-decoration: none; border: 2px solid var(--navy-deep); border-radius: 8px; font-weight: bold; transition: all 0.3s;">
                Contact Us
            </a>
        </div>

        <div style="margin-top: 60px;">
            <img src="{{ asset('assets/img/pattern.png') }}" alt="" style="width: 100px; height: auto; opacity: 0.3; transform: scaleY(-1);">
        </div>
    </div>
</section>
@endsection
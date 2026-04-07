@extends('layouts.app')

@section('title', 'Server Error — AUSHVERA')
@section('description', 'We are experiencing technical difficulties. Please try again later.')

@section('content')
<section style="padding: 200px 3rem 100px; background: var(--cream); min-height: 100vh; text-align: center;">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 40px;">
            <img src="{{ asset('assets/img/pattern.png') }}" alt="" style="width: 100px; height: auto; opacity: 0.3;">
        </div>

        <h1 style="font-size: 120px; font-weight: bold; color: var(--gold); margin-bottom: 20px; font-family: 'Cinzel', serif;">500</h1>

        <h2 style="font-size: 36px; color: var(--navy-deep); margin-bottom: 20px; font-family: 'Playfair Display', serif;">
            Server Error
        </h2>

        <p style="font-size: 18px; color: var(--charcoal); margin-bottom: 40px; line-height: 1.6;">
            We're experiencing technical difficulties. Our team has been notified and is working to resolve this issue.
        </p>

        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('home') }}" class="cta-primary" style="padding: 15px 30px; background: var(--gold); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: background 0.3s;">
                Go Home
            </a>

            <button onclick="window.location.reload()" class="cta-secondary" style="padding: 15px 30px; background: transparent; color: var(--navy-deep); text-decoration: none; border: 2px solid var(--navy-deep); border-radius: 8px; font-weight: bold; transition: all 0.3s; cursor: pointer;">
                Try Again
            </button>

            <a href="{{ route('contact') }}" class="cta-secondary" style="padding: 15px 30px; background: transparent; color: var(--navy-deep); text-decoration: none; border: 2px solid var(--navy-deep); border-radius: 8px; font-weight: bold; transition: all 0.3s;">
                Contact Support
            </a>
        </div>

        <div style="margin-top: 60px; padding: 20px; background: rgba(255,255,255,0.8); border-radius: 8px; border-left: 4px solid var(--gold);">
            <p style="font-size: 14px; color: var(--charcoal); margin: 0;">
                <strong>What happened?</strong> Something went wrong on our end. This is not your fault.
                <br><br>
                <strong>What can you do?</strong> Try refreshing the page, or come back later. If the problem persists, please contact our support team.
            </p>
        </div>

        <div style="margin-top: 40px;">
            <img src="{{ asset('assets/img/pattern.png') }}" alt="" style="width: 100px; height: auto; opacity: 0.3; transform: scaleY(-1);">
        </div>
    </div>
</section>
@endsection
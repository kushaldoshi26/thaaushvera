<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AUSHVERA — Wellness, Refined by Nature')</title>
    <meta name="description" content="@yield('description', 'Premium Ayurvedic wellness products rooted in ancient botanical wisdom.')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400;1,600&family=Inter:wght@300;400;500;600&family=Cinzel:wght@400;600&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&display=swap" rel="stylesheet">
    {{-- Use the original complete styles.css which has all nav, hero, banner, footer styles --}}
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    @stack('styles')
</head>
<body>
    @include('partials.nav')
    <main id="main-content">
        @yield('content')
    </main>
    @include('partials.footer')
    @stack('scripts')
</body>
</html>

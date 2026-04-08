<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Primary Meta Tags -->
<title>@yield('title', 'AUSHVERA — Wellness, Refined by Nature')</title>
<meta name="title" content="@yield('title', 'AUSHVERA — Wellness, Refined by Nature')">
<meta name="description" content="@yield('description', 'Premium Ayurvedic wellness products rooted in ancient botanical wisdom.')">
<meta name="keywords" content="ayurveda, wellness, ayurvedic products, herbal medicine, holistic health, botanicals">
<meta name="author" content="Aushvera">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('title', 'AUSHVERA — Wellness, Refined by Nature')">
<meta property="og:description" content="@yield('description', 'Premium Ayurvedic wellness products rooted in ancient botanical wisdom.')">
<meta property="og:image" content="@yield('og_image', asset('assets/img/og-banner.jpg'))">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ url()->current() }}">
<meta property="twitter:title" content="@yield('title', 'AUSHVERA — Wellness, Refined by Nature')">
<meta property="twitter:description" content="@yield('description', 'Premium Ayurvedic wellness products rooted in ancient botanical wisdom.')">
<meta property="twitter:image" content="@yield('og_image', asset('assets/img/og-banner.jpg'))">

<!-- Canonical -->
<link rel="canonical" href="{{ url()->current() }}">

<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

<!-- Structured Data -->
@yield('structured_data')

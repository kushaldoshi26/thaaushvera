{{-- SEO Meta Tags --}}
<meta name="description" content="@yield('meta_description', 'AUSHVERA — Premium botanical elixirs and wellness essentials. Refined by nature for your daily ritual.')">
<meta name="keywords" content="@yield('meta_keywords', 'ayurveda, premium wellness, botanical elixirs, organic skincare, natural supplements, AUSHVERA')">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('title', 'AUSHVERA — Wellness, Refined by Nature')">
<meta property="og:description" content="@yield('meta_description', 'AUSHVERA — Premium botanical elixirs and wellness essentials.')">
<meta property="og:image" content="@yield('og_image', asset('assets/img/logo.png'))">

{{-- Twitter --}}
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ url()->current() }}">
<meta property="twitter:title" content="@yield('title', 'AUSHVERA — Wellness, Refined by Nature')">
<meta property="twitter:description" content="@yield('meta_description', 'AUSHVERA — Premium botanical elixirs and wellness essentials.')">
<meta property="twitter:image" content="@yield('og_image', asset('assets/img/logo.png'))">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ url()->current() }}">

{{-- Structured Data (Organization) --}}
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Organization",
  "name": "AUSHVERA",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('assets/img/logo.png') }}",
  "sameAs": [
    "https://facebook.com/aushvera",
    "https://instagram.com/aushvera"
  ]
}
</script>

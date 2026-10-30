<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0c0d11">
    <meta name="description" content="@yield('meta_description', 'SAQR Motors — motorcycle showroom, promotions and test rides')">
    <title>@yield('title', 'SAQR Motors')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/saqr-favicon.png') }}">
    @vite(['resources/css/public.css', 'resources/js/public.js'])
</head>
<body class="bg-[#0c0d11] text-zinc-100 antialiased selection:bg-red-600/30">
    <x-public.navbar />
    <main id="main-content">@yield('content')</main>
    <x-public.footer />
</body>
</html>

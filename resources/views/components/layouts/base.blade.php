@props(['title' => null, 'description' => null, 'canonical' => null, 'image' => null, 'robots' => null])
@php
    $pageTitle = ($title ? $title.' - ' : '').$siteName;
    $pageDescription = $description ?? 'Buat undangan pernikahan online sendiri: pilih dari puluhan tema, isi data, bayar, lalu bagikan lewat WhatsApp dalam hitungan menit.';
    $pageUrl = $canonical ?? url()->current();
    $pageImage = $image ?? asset('og-image.png');
    // Only production may be indexed; staging and local are always noindex.
    $pageRobots = app()->isProduction() ? ($robots ?? 'index, follow') : 'noindex, nofollow';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="{{ $pageRobots }}">
    <link rel="canonical" href="{{ $pageUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#b76e79">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {{ $head ?? '' }}
</head>
<body {{ $attributes->merge(['class' => 'min-h-screen bg-cream font-sans text-ink antialiased']) }}>
    {{ $slot }}
    @livewireScripts
</body>
</html>

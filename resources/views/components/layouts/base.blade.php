@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' - ' : '' }}{{ $siteName }}</title>
    <meta name="description" content="{{ $description ?? 'Buat undangan pernikahan online sendiri: pilih tema, isi data, bayar, dan bagikan dalam hitungan menit.' }}">
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

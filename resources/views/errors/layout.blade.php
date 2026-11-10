{{--
    Shared error page. Kept free of database and session lookups (site name
    comes from config, not settings) so it still renders during a 500 or 503.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') - {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-16 text-center">
        <a href="{{ config('app.url') }}" class="font-serif text-2xl text-rose-brand-500 italic">{{ config('app.name') }}</a>
        <p class="mt-10 font-serif text-6xl font-semibold text-rose-brand-200">@yield('code')</p>
        <h1 class="mt-4 font-serif text-3xl font-semibold">@yield('title')</h1>
        <p class="mt-3 max-w-md text-gray-500">@yield('message')</p>
        <a href="{{ config('app.url') }}" class="btn btn-outline mt-8">Kembali ke beranda</a>
    </main>
</body>
</html>

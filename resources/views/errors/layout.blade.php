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
    @fonts(['inter', 'cormorant-garamond'])
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">
    <main class="relative flex min-h-screen flex-col overflow-hidden px-4 py-8 sm:px-10">
        <x-brand.mark class="pointer-events-none absolute top-1/2 -right-24 h-[34rem] w-[34rem] -translate-y-1/2 text-rose-brand-100/70 sm:right-[-6rem] sm:h-[44rem] sm:w-[44rem]"/>

        <a href="{{ config('app.url') }}" class="relative w-fit" aria-label="{{ config('app.name') }}"><x-brand.logo/></a>

        <div class="relative mx-auto flex w-full max-w-6xl flex-1 flex-col justify-center py-16">
            <p class="eyebrow">Kesalahan @yield('code')</p>
            <p class="mt-6 font-display text-[clamp(6rem,22vw,15rem)] leading-[0.8] tracking-[-0.04em] text-rose-brand-300 italic [font-variant-numeric:lining-nums]" aria-hidden="true">@yield('code')</p>
            <h1 class="page-title mt-8 max-w-2xl">@yield('title')</h1>
            <p class="mt-5 max-w-md text-gray-600">@yield('message')</p>
            <a href="{{ config('app.url') }}" class="btn btn-primary btn-lg btn-arrow mt-10 w-fit">
                Kembali ke beranda
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
            </a>
        </div>
    </main>
</body>
</html>

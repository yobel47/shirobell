@props(['invitation', 'preview' => false, 'demo' => false, 'musicUrl' => null, 'css', 'title' => null, 'coverUrl' => null, 'themeStyle' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @unless ($demo)
        <meta name="robots" content="noindex, nofollow">
    @endunless
    <title>{{ $title ?? 'Undangan Pernikahan '.$invitation->coupleName() }}</title>
    <meta name="description" content="Undangan pernikahan {{ $invitation->coupleName() }}">
    <meta property="og:title" content="The Wedding of {{ $invitation->coupleName() }}">
    <meta property="og:description" content="Kami mengundang Anda untuk hadir di hari bahagia kami.">
    @if ($coverUrl)
        <meta property="og:image" content="{{ url($coverUrl) }}">
    @endif
    @fonts
    @vite([$css])
    @livewireStyles
</head>
<body {{ $attributes->merge(['class' => 'antialiased']) }} @if ($themeStyle) style="{{ $themeStyle }}" @endif>
    @if ($demo)
        <div class="fixed inset-x-0 top-0 z-[70] flex items-center justify-center gap-3 bg-black/85 px-4 py-2 text-xs text-white">
            <span>Contoh tema {{ $invitation->theme->name }}</span>
            <a href="{{ route('register') }}" class="rounded-full bg-white px-3 py-1 font-semibold text-black">Buat undangan seperti ini</a>
        </div>
    @endif
    {{-- Inside the editor's preview frame (?frame=1) the banner is hidden; the watermark stays. --}}
    @if ($preview && ! request()->boolean('frame'))
        <div class="fixed inset-x-0 top-0 z-[70] bg-black/80 px-4 py-2 text-center text-xs text-white">
            Mode preview: undangan belum diterbitkan dan hanya terlihat oleh Anda.
            <a href="{{ route('app.invitations.edit', $invitation) }}" class="font-semibold underline">Kembali ke editor</a>
        </div>
    @endif
    @if ($preview)
        <div class="pointer-events-none fixed inset-0 z-[60] flex select-none flex-col items-center justify-around overflow-hidden" aria-hidden="true">
            @for ($i = 0; $i < 6; $i++)
                <span class="-rotate-30 font-sans text-6xl font-bold tracking-[0.4em] text-black/10 sm:text-7xl">PREVIEW</span>
            @endfor
        </div>
    @endif

    {{ $slot }}

    @if ($musicUrl)
        <audio x-ref="music" src="{{ $musicUrl }}" loop preload="none"></audio>
        <button type="button" x-show="opened" x-cloak x-on:click="toggleMusic()"
                class="fixed right-4 bottom-4 z-50 flex h-12 w-12 items-center justify-center rounded-full bg-white/90 text-gray-800 shadow-lg ring-1 ring-black/10 backdrop-blur"
                x-bind:aria-label="playing ? 'Matikan musik' : 'Putar musik'">
            <svg x-show="playing" class="h-5 w-5 animate-spin [animation-duration:4s]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18V5l12-2v13M9 18a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm12-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
            <svg x-show="!playing" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18V5l12-2v13M9 18a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm12-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM3 3l18 18"/></svg>
        </button>
    @endif

    @livewireScripts
</body>
</html>

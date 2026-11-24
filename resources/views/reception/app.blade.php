<x-layouts.base title="Penerima Tamu" robots="noindex, nofollow" class="min-h-screen bg-cream font-sans text-ink antialiased">
    <x-slot:head>
        @vite('resources/js/checkin.js')
    </x-slot:head>
    <header class="sticky top-0 z-30 border-b border-rose-brand-900/8 bg-cream/85 backdrop-blur-md">
        <div class="mx-auto flex max-w-2xl items-center justify-between gap-3 px-4 py-3">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold tracking-[0.12em] text-rose-brand-600 uppercase">Penerima Tamu</p>
                <p class="truncate font-serif text-2xl font-medium">{{ $invitation->coupleName() }}</p>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('reception.screen', $token) }}" target="_blank" class="btn btn-outline px-3 py-1.5 text-xs">Layar sapa</a>
                <form method="POST" action="{{ route('reception.logout', $token) }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost px-3 py-1.5 text-xs">Keluar</button>
                </form>
            </div>
        </div>
    </header>
    <main class="mx-auto max-w-2xl px-4 py-6">
        <livewire:reception.checkin-app :invitation="$invitation" />
    </main>
</x-layouts.base>

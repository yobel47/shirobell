<x-layouts.app title="Undangan Saya">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-serif text-3xl font-semibold">Undangan Saya</h1>
            <p class="mt-1 text-sm text-gray-500">Halo, {{ auth()->user()->name }}. Kelola semua undangan Anda di sini.</p>
        </div>
        @if ($invitations->isNotEmpty() && $canCreate)
            <a href="{{ route('app.invitations.create') }}" class="btn btn-primary">Buat undangan baru</a>
        @endif
    </div>

    @if ($invitations->isEmpty())
        <div class="card mt-8 flex flex-col items-center px-6 py-14 text-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-rose-brand-50 text-rose-brand-500">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h2 class="mt-4 font-serif text-xl font-semibold">Belum ada undangan</h2>
            <p class="mt-1 max-w-sm text-sm text-gray-500">Buat undangan pertama Anda: pilih alamat, pilih tema, lalu isi datanya. Gratis dicoba sebelum diterbitkan.</p>
            <a href="{{ route('app.invitations.create') }}" class="btn btn-primary mt-6">Buat undangan</a>
        </div>
    @else
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($invitations as $invitation)
                <article class="card flex flex-col overflow-hidden p-0">
                    <x-theme-swatch :theme="$invitation->theme" class="h-32" />
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="font-serif text-lg font-semibold">{{ $invitation->coupleName() }}</h2>
                            <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $invitation->status->badgeClass() }}">{{ $invitation->status->label() }}</span>
                        </div>
                        <p class="mt-1 truncate text-sm text-gray-500">{{ $invitation->slug }}.{{ config('app.domain') }}</p>
                        <p class="mt-1 text-xs text-gray-400">
                            Tema {{ $invitation->theme->name }}
                            @if ($invitation->active_until)
                                &middot; Aktif sampai {{ $invitation->active_until->translatedFormat('d M Y') }}
                            @endif
                        </p>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <a href="{{ route('app.invitations.edit', $invitation) }}" class="btn btn-primary px-4 py-1.5">Edit</a>
                            @if ($invitation->status === \App\Enums\InvitationStatus::Draft)
                                <a href="{{ route('app.checkout', $invitation) }}" class="btn btn-outline px-4 py-1.5">Bayar</a>
                            @else
                                <a href="{{ route('app.invitations.guests', $invitation) }}" class="btn btn-outline px-4 py-1.5">Tamu</a>
                            @endif
                            <a href="{{ route('app.invitations.preview', $invitation) }}" target="_blank" rel="noopener" class="btn btn-ghost px-4 py-1.5">Preview</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        @unless ($canCreate)
            <p class="mt-6 text-sm text-gray-500">Anda sudah mencapai batas undangan draft. Terbitkan atau hapus salah satu untuk membuat yang baru.</p>
        @endunless
    @endif
</x-layouts.app>

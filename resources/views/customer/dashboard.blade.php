<x-layouts.app title="Undangan Saya">
    <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="eyebrow">Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }}</p>
            <h1 class="page-title mt-5">Undangan <em>Saya</em></h1>
            <p class="mt-4 text-gray-600">Kelola semua undangan Anda di sini.</p>
        </div>
        @if ($invitations->isNotEmpty() && $canCreate)
            <a href="{{ route('app.invitations.create') }}" class="btn btn-primary btn-arrow shrink-0">
                Buat undangan baru
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
            </a>
        @endif
    </div>

    @if ($invitations->isEmpty())
        {{-- Empty state: a blank invitation waiting to be written --}}
        <div class="mt-12 grid items-center gap-10 overflow-hidden rounded-3xl bg-rose-brand-50 px-6 py-12 sm:px-12 md:grid-cols-[1fr_auto]">
            <div class="max-w-md">
                <h2 class="font-serif text-3xl font-medium">Belum ada undangan.</h2>
                <p class="mt-3 text-gray-600">Buat undangan pertama Anda: pilih alamat, pilih tema, lalu isi datanya. Gratis dicoba sebelum diterbitkan.</p>
                <a href="{{ route('app.invitations.create') }}" class="btn btn-primary btn-lg btn-arrow mt-8">
                    Buat undangan
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div>
            <div class="mx-auto w-44 rotate-3 rounded-[1.5rem] border border-dashed border-rose-brand-300 bg-cream p-2" aria-hidden="true">
                <div class="flex aspect-[3/4] flex-col items-center justify-center gap-3 rounded-[1.1rem] border border-rose-brand-100 px-4 text-center">
                    <x-brand.mark class="h-6 w-6 text-rose-brand-300"/>
                    <span class="font-display text-2xl text-rose-brand-300 italic">Nama &amp; Nama</span>
                    <span class="h-px w-10 bg-rose-brand-200"></span>
                    <span class="text-[10px] tracking-[0.2em] text-rose-brand-300 uppercase">Segera</span>
                </div>
            </div>
        </div>
    @else
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($invitations as $invitation)
                <article class="group card flex flex-col overflow-hidden p-0 transition duration-300 ease-out-expo hover:-translate-y-1 hover:shadow-[0_28px_48px_-28px_rgb(89_52_59/0.4)]">
                    <div class="relative overflow-hidden">
                        <x-theme-swatch :theme="$invitation->theme" class="h-44 transition duration-700 ease-out-expo group-hover:scale-[1.03]" />
                        <span class="absolute top-3 right-3 rounded-full px-2.5 py-1 text-xs font-semibold shadow-sm {{ $invitation->status->badgeClass() }}">{{ $invitation->status->label() }}</span>
                    </div>
                    <div class="flex flex-1 flex-col p-6">
                        <h2 class="font-serif text-2xl leading-tight font-medium">{{ $invitation->coupleName() }}</h2>
                        <p class="mt-2 truncate font-mono text-xs text-gray-500"><span class="text-rose-brand-600">{{ $invitation->slug }}</span>.{{ config('app.domain') }}</p>
                        <p class="mt-1 mb-6 text-xs text-gray-400">
                            Tema {{ $invitation->theme->name }}
                            @if ($invitation->active_until)
                                &middot; Aktif sampai {{ $invitation->active_until->translatedFormat('d M Y') }}
                            @endif
                        </p>
                        <div class="mt-auto flex flex-wrap gap-2 border-t border-rose-brand-900/8 pt-5">
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
            <p class="mt-8 text-sm text-gray-500">Anda sudah mencapai batas undangan draft. Terbitkan atau hapus salah satu untuk membuat yang baru.</p>
        @endunless
    @endif
</x-layouts.app>

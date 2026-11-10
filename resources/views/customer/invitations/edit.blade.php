<x-layouts.app :title="'Edit '.$invitation->coupleName()">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('app.dashboard') }}" class="text-sm text-gray-500 hover:text-rose-brand-600">&larr; Undangan Saya</a>
            <h1 class="mt-1 truncate font-serif text-2xl font-semibold sm:text-3xl">{{ $invitation->coupleName() }}</h1>
            <p class="mt-1 truncate text-sm text-gray-500">{{ $invitation->slug }}.{{ config('app.domain') }}</p>
        </div>
        <div class="flex shrink-0 gap-2">
            <a href="{{ route('app.invitations.preview', $invitation) }}" target="_blank" rel="noopener" class="btn btn-outline">Preview</a>
            @if ($invitation->isActive())
                <a href="{{ $invitation->publicUrl() }}" target="_blank" rel="noopener" class="btn btn-primary">Buka undangan</a>
            @endif
        </div>
    </div>

    @php $status = $invitation->status; @endphp
    <div @class([
        'mt-5 flex flex-col gap-1 rounded-xl px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between',
        'bg-amber-50 text-amber-800' => $status === \App\Enums\InvitationStatus::Draft,
        'bg-emerald-50 text-emerald-800' => $status === \App\Enums\InvitationStatus::Active,
        'bg-gray-100 text-gray-700' => $status === \App\Enums\InvitationStatus::Expired,
        'bg-red-50 text-red-700' => $status === \App\Enums\InvitationStatus::Suspended,
    ])>
        @switch($status)
            @case(\App\Enums\InvitationStatus::Draft)
                <span><strong>Draft.</strong> Undangan hanya bisa dilihat Anda lewat Preview (dengan watermark). Pembayaran untuk menerbitkan segera tersedia.</span>
                @break
            @case(\App\Enums\InvitationStatus::Active)
                <span><strong>Aktif</strong> sampai {{ $invitation->active_until?->translatedFormat('d F Y') }}.</span>
                @break
            @case(\App\Enums\InvitationStatus::Expired)
                <span><strong>Masa aktif berakhir.</strong> Perpanjang untuk menampilkan undangan lagi.</span>
                @break
            @default
                <span><strong>Ditangguhkan.</strong> {{ $invitation->suspended_reason ?: 'Hubungi admin untuk informasi lebih lanjut.' }}</span>
        @endswitch
    </div>

    <div class="mt-6 lg:grid lg:grid-cols-[13rem_1fr] lg:gap-8">
        <nav aria-label="Bagian editor" class="-mx-4 mb-6 overflow-x-auto px-4 lg:mx-0 lg:mb-0 lg:px-0">
            <ul class="flex gap-1 lg:sticky lg:top-24 lg:flex-col">
                @foreach ($tabs as $key => [$label])
                    <li>
                        <a href="{{ route('app.invitations.edit', [$invitation, $key]) }}"
                           @if ($tab === $key) aria-current="page" @endif
                           @class([
                               'block rounded-full px-4 py-2 text-sm font-medium whitespace-nowrap lg:rounded-xl',
                               'bg-rose-brand-500 text-white' => $tab === $key,
                               'text-ink hover:bg-rose-brand-50' => $tab !== $key,
                           ])>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="min-w-0">
            @livewire($tabs[$tab][1], ['invitation' => $invitation], key($tab))
        </div>
    </div>
</x-layouts.app>

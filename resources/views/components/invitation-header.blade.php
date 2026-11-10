@props(['invitation', 'active' => 'editor'])
@use('App\Enums\InvitationStatus')
@php
    $status = $invitation->status;
    $sections = [
        'editor' => ['Editor', route('app.invitations.edit', $invitation)],
        'tamu' => ['Tamu', route('app.invitations.guests', $invitation)],
        'rsvp' => ['RSVP & Ucapan', route('app.invitations.wishes', $invitation)],
    ];
@endphp
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

<div @class([
    'mt-5 flex flex-col gap-3 rounded-xl px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between',
    'bg-amber-50 text-amber-800' => $status === InvitationStatus::Draft,
    'bg-emerald-50 text-emerald-800' => $status === InvitationStatus::Active,
    'bg-gray-100 text-gray-700' => $status === InvitationStatus::Expired,
    'bg-red-50 text-red-700' => $status === InvitationStatus::Suspended,
])>
    <span>
        @switch($status)
            @case(InvitationStatus::Draft)
                <strong>Draft.</strong> Undangan hanya bisa dilihat Anda lewat Preview (dengan watermark). Terbitkan agar bisa dibuka tamu.
                @break
            @case(InvitationStatus::Active)
                <strong>Aktif</strong> sampai {{ $invitation->active_until?->translatedFormat('d F Y') }}.
                @break
            @case(InvitationStatus::Expired)
                <strong>Masa aktif berakhir.</strong> Perpanjang untuk menampilkan undangan lagi.
                @break
            @default
                <strong>Ditangguhkan.</strong> {{ $invitation->suspended_reason ?: 'Hubungi admin untuk informasi lebih lanjut.' }}
        @endswitch
    </span>
    @if ($status !== InvitationStatus::Suspended)
        <a href="{{ route('app.checkout', $invitation) }}" @class([
            'btn shrink-0 px-4 py-1.5',
            'btn-primary' => $status !== InvitationStatus::Active,
            'btn-outline' => $status === InvitationStatus::Active,
        ])>{{ $status === InvitationStatus::Draft ? 'Bayar & terbitkan' : 'Perpanjang' }}</a>
    @endif
</div>

<nav aria-label="Menu undangan" class="mt-5 border-b border-rose-brand-100">
    <ul class="-mb-px flex gap-4 overflow-x-auto">
        @foreach ($sections as $key => [$label, $url])
            <li>
                <a href="{{ $url }}" @if ($active === $key) aria-current="page" @endif @class([
                    'block border-b-2 px-1 py-2.5 text-sm font-medium whitespace-nowrap',
                    'border-rose-brand-500 text-rose-brand-700' => $active === $key,
                    'border-transparent text-gray-500 hover:text-ink' => $active !== $key,
                ])>{{ $label }}</a>
            </li>
        @endforeach
    </ul>
</nav>

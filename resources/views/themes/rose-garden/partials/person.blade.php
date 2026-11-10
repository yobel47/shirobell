@php
    $nickname = $invitation->{$side.'_nickname'};
    $fullname = $invitation->{$side.'_fullname'} ?: $nickname;
    $childOrder = $invitation->{$side.'_child_order'};
    $father = $invitation->{$side.'_father'};
    $mother = $invitation->{$side.'_mother'};
    $instagram = $invitation->{$side.'_instagram'};
@endphp
<div>
    <div class="rg-arch mx-auto h-64 w-48 overflow-hidden border-4 border-white bg-rg-blush shadow-lg">
        @if ($photo)
            <img src="{{ $photo }}" alt="Foto {{ $fullname }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full items-center justify-center font-script text-6xl text-rg-rose">{{ mb_substr((string) $nickname, 0, 1) }}</div>
        @endif
    </div>
    <h3 class="mt-6 font-script text-5xl text-rg-deep">{{ $nickname }}</h3>
    <p class="mt-2 font-serif text-lg font-semibold">{{ $fullname }}</p>
    @if ($father || $mother)
        <p class="mt-2 text-sm text-rg-ink/80">
            {{ $childOrder ?: ($side === 'groom' ? 'Putra dari' : 'Putri dari') }}<br>
            {{ $father }}@if ($father && $mother) &amp; @endif{{ $mother }}
        </p>
    @endif
    @if ($instagram)
        <a href="https://instagram.com/{{ $instagram }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block rounded-full border border-rg-rose/40 px-4 py-1.5 text-xs font-medium text-rg-deep hover:bg-rg-blush">{{ '@'.$instagram }}</a>
    @endif
</div>

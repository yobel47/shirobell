@php
    $nickname = $invitation->{$side.'_nickname'};
    $fullname = $invitation->{$side.'_fullname'} ?: $nickname;
    $childOrder = $invitation->{$side.'_child_order'};
    $father = $invitation->{$side.'_father'};
    $mother = $invitation->{$side.'_mother'};
    $instagram = $invitation->{$side.'_instagram'};
@endphp
<div>
    <div class="sm-arch mx-auto h-48 w-48 overflow-hidden bg-sm-blush">
        @if ($photo)
            <img src="{{ $photo }}" alt="Foto {{ $fullname }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full items-center justify-center font-script text-6xl text-sm-rose">{{ mb_substr((string) $nickname, 0, 1) }}</div>
        @endif
    </div>
    <h3 class="mt-6 font-script text-5xl text-sm-deep">{{ $nickname }}</h3>
    <p class="mt-2 font-serif text-lg font-semibold">{{ $fullname }}</p>
    @if ($father || $mother)
        <p class="mt-2 text-sm text-sm-ink/80">
            {{ $childOrder ?: ($side === 'groom' ? 'Putra dari' : 'Putri dari') }}<br>
            {{ $father }}@if ($father && $mother) &amp; @endif{{ $mother }}
        </p>
    @endif
    @if ($instagram)
        <a href="https://instagram.com/{{ $instagram }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block rounded-full border border-sm-rose/40 px-4 py-1.5 text-xs font-medium text-sm-deep hover:bg-sm-blush">{{ '@'.$instagram }}</a>
    @endif
</div>

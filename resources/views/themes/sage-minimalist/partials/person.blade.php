@php
    $nickname = $invitation->{$side.'_nickname'};
    $fullname = $invitation->{$side.'_fullname'} ?: $nickname;
    $childOrder = $invitation->{$side.'_child_order'};
    $father = $invitation->{$side.'_father'};
    $mother = $invitation->{$side.'_mother'};
    $instagram = $invitation->{$side.'_instagram'};
@endphp
<div class="tc-stagger" {!! $reveal !!}>
    <div class="sm-arch mx-auto h-48 w-48 overflow-hidden bg-sm-blush">
        @if ($photo)
            <img src="{{ $photo }}" alt="Foto {{ $fullname }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full items-center justify-center font-script text-6xl text-sm-rose">{{ mb_substr((string) $nickname, 0, 1) }}</div>
        @endif
    </div>
    <div>
        <h3 class="mt-7 font-script text-5xl text-sm-deep">{{ $nickname }}</h3>
        <p class="tc-title mt-2 font-serif text-lg font-semibold">{{ $fullname }}</p>
    </div>
    @if ($father || $mother)
        <p class="mt-3 text-sm leading-relaxed text-sm-ink/80">
            {{ $childOrder ?: ($side === 'groom' ? 'Putra dari' : 'Putri dari') }}<br>
            {{ $father }}@if ($father && $mother) &amp; @endif{{ $mother }}
        </p>
    @endif
    @if ($instagram)
        <a href="https://instagram.com/{{ $instagram }}" target="_blank" rel="noopener noreferrer" class="tc-btn-outline mt-4 px-4 py-2"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>{{ '@'.$instagram }}</a>
    @endif
</div>

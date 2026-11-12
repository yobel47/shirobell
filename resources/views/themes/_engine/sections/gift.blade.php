{{-- Section "gift" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Digital gift --}}
@if ($giftAccounts->isNotEmpty())
    @include('themes._engine.partials.gift')
@endif

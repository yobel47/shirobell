{{-- Section "opening" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Opening & quote --}}
<section class="px-8 py-10 text-center" {!! $reveal !!}>
    @if ($invitation->quote_text)
        <blockquote class="font-heading text-base leading-relaxed italic opacity-90">"{{ $invitation->quote_text }}"</blockquote>
        @if ($invitation->quote_source)
            <p class="mt-3 text-xs font-semibold tracking-widest text-t-accent uppercase">{{ $invitation->quote_source }}</p>
        @endif
    @endif
    @if ($invitation->opening_text)
        <p @class(['text-sm leading-relaxed', 'mt-10' => $invitation->quote_text])>{{ $invitation->opening_text }}</p>
    @endif
</section>

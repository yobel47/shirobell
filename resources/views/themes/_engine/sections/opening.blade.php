{{-- Section "opening" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Opening & quote: a large accent quotation mark frames the verse. --}}
<section class="px-8 py-14 text-center" {!! $reveal !!}>
    @if ($invitation->quote_text)
        <figure class="relative">
            <span class="block font-heading text-7xl leading-[0.5] text-t-accent/50" aria-hidden="true">&ldquo;</span>
            <blockquote class="tc-pretty mt-4 font-heading text-lg leading-relaxed italic opacity-90">{{ $invitation->quote_text }}</blockquote>
            @if ($invitation->quote_source)
                <figcaption class="tc-eyebrow mt-5">{{ $invitation->quote_source }}</figcaption>
            @endif
        </figure>
    @endif
    @if ($invitation->opening_text)
        <p @class(['tc-pretty mx-auto max-w-sm text-[0.95rem] leading-[1.8]', 'mt-12' => $invitation->quote_text])>{{ $invitation->opening_text }}</p>
    @endif
</section>

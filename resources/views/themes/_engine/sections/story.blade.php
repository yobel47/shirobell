{{-- Section "story" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Love story --}}
@if ($stories->isNotEmpty())
    <section class="relative bg-t-soft/60 px-6 py-16">
        @include('themes._engine.ornaments.crown', ['class' => 'h-9 w-36'])
        <h2 class="{{ $h2 }} text-center">Cerita Kami</h2>
        <ol class="relative mt-10 space-y-10 border-l border-t-accent/50 pl-6">
            @foreach ($stories as $story)
                <li class="relative" {!! $reveal !!}>
                    <span class="absolute top-1.5 -left-[31px] h-3 w-3 rounded-full bg-t-accent ring-4 ring-t-soft"></span>
                    @if ($story->story_date)
                        <p class="text-xs font-semibold tracking-widest text-t-accent uppercase">{{ $story->story_date }}</p>
                    @endif
                    <h3 class="mt-1 font-heading text-xl font-semibold text-t-primary">{{ $story->title }}</h3>
                    @if ($story->photoUrl())
                        <img src="{{ $story->photoUrl() }}" alt="" class="{{ $shape === 't-shape-circle' ? 't-shape-rounded' : $shape }} mt-3 w-full object-cover" loading="lazy">
                    @endif
                    <p class="mt-3 text-sm leading-relaxed whitespace-pre-line">{{ $story->body }}</p>
                </li>
            @endforeach
        </ol>
    </section>
@endif

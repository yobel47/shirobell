{{-- Section "story" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Love story --}}
@if ($stories->isNotEmpty())
    <section class="relative bg-t-soft/60 px-6 py-20">
        @include('themes._engine.partials.heading', ['eyebrow' => 'Perjalanan Kami', 'title' => 'Cerita Kami'])
        <ol class="relative mt-12 space-y-12 border-l border-t-accent/50 pl-7">
            @foreach ($stories as $story)
                <li class="relative" {!! $reveal !!}>
                    <span class="absolute top-1 -left-[35px] h-3.5 w-3.5 rotate-45 border border-t-accent bg-t-soft"></span>
                    @if ($story->story_date)
                        <p class="tc-eyebrow tc-eyebrow-start tc-num">{{ $story->story_date }}</p>
                    @endif
                    <h3 class="tc-title mt-2 font-heading text-2xl font-semibold text-t-primary">{{ $story->title }}</h3>
                    @if ($story->photoUrl())
                        <img src="{{ $story->photoUrl() }}" alt="" class="{{ $shape === 't-shape-circle' ? 't-shape-rounded' : $shape }} mt-4 w-full object-cover shadow-sm" loading="lazy">
                    @endif
                    <p class="tc-pretty mt-4 text-sm leading-[1.8] whitespace-pre-line">{{ $story->body }}</p>
                </li>
            @endforeach
        </ol>
    </section>
@endif

{{-- Section "gallery" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Gallery --}}
@if ($photos->isNotEmpty())
    <section class="px-4 py-20"
             x-data="{ show: false, index: 0, photos: @js($photos->map->url()->values()) }"
             x-on:keydown.escape.window="show = false">
        @include('themes._engine.partials.heading', ['eyebrow' => 'Momen Kami', 'title' => 'Galeri'])
        <div class="mt-10 grid grid-cols-2 gap-2" {!! $reveal !!}>
            @foreach ($photos as $photo)
                <button type="button" x-on:click="index = {{ $loop->index }}; show = true" aria-label="Buka foto {{ $loop->iteration }}"
                        @class(['tc-zoom', $engine['shape'] === 'square' ? 'overflow-hidden' : 'overflow-hidden rounded-2xl', 'col-span-2' => $loop->index % 5 === 0])>
                    <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?: 'Foto '.$loop->iteration }}" loading="lazy"
                         @class(['w-full object-cover', 'aspect-[4/3]' => $loop->index % 5 === 0, 'aspect-square' => $loop->index % 5 !== 0])>
                </button>
            @endforeach
        </div>
        <x-invitation.lightbox />
    </section>
@endif

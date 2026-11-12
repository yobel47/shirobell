{{-- Section "gallery" of the engine theme (ADR 012: order and visibility are configurable). --}}
{{-- Gallery --}}
@if ($photos->isNotEmpty())
    <section class="px-4 py-16"
             x-data="{ show: false, index: 0, photos: @js($photos->map->url()->values()) }"
             x-on:keydown.escape.window="show = false">
        @include('themes._engine.ornaments.crown', ['class' => 'h-9 w-36'])
        <h2 class="{{ $h2 }} text-center">Galeri</h2>
        <div class="mt-8 grid grid-cols-2 gap-2" {!! $reveal !!}>
            @foreach ($photos as $photo)
                <button type="button" x-on:click="index = {{ $loop->index }}; show = true"
                        @class([$engine['shape'] === 'square' ? 'overflow-hidden' : 'overflow-hidden rounded-2xl', 'col-span-2' => $loop->index % 5 === 0])>
                    <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?: 'Foto '.$loop->iteration }}" loading="lazy"
                         @class(['w-full object-cover transition duration-500 hover:scale-105', 'aspect-[4/3]' => $loop->index % 5 === 0, 'aspect-square' => $loop->index % 5 !== 0])>
                </button>
            @endforeach
        </div>
        <div x-show="show" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4" x-on:click.self="show = false">
            <img x-bind:src="photos[index]" alt="" class="max-h-[85vh] max-w-full rounded-lg object-contain">
            <button type="button" x-on:click="show = false" class="absolute top-4 right-4 rounded-full bg-white/10 px-3 py-1 text-sm text-white" aria-label="Tutup">Tutup</button>
            <button type="button" x-on:click="index = (index - 1 + photos.length) % photos.length" class="absolute left-2 rounded-full bg-white/10 px-3 py-2 text-white" aria-label="Sebelumnya">&lsaquo;</button>
            <button type="button" x-on:click="index = (index + 1) % photos.length" class="absolute right-2 rounded-full bg-white/10 px-3 py-2 text-white" aria-label="Berikutnya">&rsaquo;</button>
        </div>
    </section>
@endif

{{-- Themes: full-bleed horizontal rail, first card aligned to the content column. --}}
<section id="tema" class="scroll-mt-20 overflow-hidden bg-white py-20 sm:py-28" x-data="{ scroll(dir) { const r = this.$refs.rail; r.scrollBy({ left: dir * r.clientWidth * 0.8, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }) } }">
    <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 sm:px-6 md:flex-row md:items-end md:justify-between" data-reveal>
        <div>
            <p class="eyebrow">{{ $themes->count() }} tema</p>
            <h2 class="display mt-6 text-[clamp(2.5rem,5vw,4.5rem)] text-ink">Pilih <em>wajah</em> undangan Anda.</h2>
            <p class="mt-5 max-w-xl text-gray-600">Tema elegan dengan beberapa pilihan warna, dirancang untuk tampil sempurna di layar HP.</p>
        </div>
        @if ($themes->count() > 1)
            <div class="hidden shrink-0 gap-2 md:flex">
                @foreach ([-1 => ['Tema sebelumnya', 'M15 18l-6-6 6-6'], 1 => ['Tema berikutnya', 'M9 6l6 6-6 6']] as $dir => [$label, $path])
                    <button type="button" x-on:click="scroll({{ $dir }})" aria-label="{{ $label }}"
                            class="flex h-12 w-12 items-center justify-center rounded-full border border-rose-brand-900/15 text-ink transition hover:border-rose-brand-400 hover:text-rose-brand-600 focus-visible:ring-2 focus-visible:ring-rose-brand-400 focus-visible:outline-none">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    @if ($themes->isEmpty())
        <p class="mt-12 text-center text-sm text-gray-500">Tema segera hadir.</p>
    @else
        <div x-ref="rail" class="rail mt-12 flex snap-x snap-mandatory gap-5 overflow-x-auto pb-4" tabindex="0" role="region" aria-label="Daftar tema">
            @foreach ($themes->take(8) as $theme)
                <div class="w-[78%] max-w-[20rem] shrink-0 snap-start sm:w-80" data-reveal style="--d: {{ min($loop->index, 3) * 70 }}ms">
                    <x-theme-card :theme="$theme" />
                </div>
            @endforeach
            <a href="{{ route('themes.index') }}" class="group flex w-[60%] max-w-[16rem] shrink-0 snap-start flex-col items-start justify-end rounded-2xl bg-rose-brand-50 p-6 sm:w-64">
                <span class="font-display text-5xl leading-none text-ink italic">{{ $themes->count() }}</span>
                <span class="mt-2 font-semibold text-ink">Lihat semua tema</span>
                <span class="mt-4 flex h-10 w-10 items-center justify-center rounded-full bg-rose-brand-500 text-white transition group-hover:translate-x-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </span>
            </a>
        </div>
    @endif
</section>

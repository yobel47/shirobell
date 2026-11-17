{{--
    Gallery lightbox shared by every theme. Lives inside the gallery's Alpine
    scope ({ show, index, photos }). Swipe on touch, arrow keys on desktop,
    a photo counter, and the image crossfades between photos.
--}}
<div x-show="show" x-cloak
     x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0" x-transition:leave="transition duration-200" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex flex-col bg-black/92 text-white" role="dialog" aria-modal="true" aria-label="Galeri foto"
     x-data="{
        sx: null,
        go(step) { this.index = (this.index + step + this.photos.length) % this.photos.length },
        start(e) { this.sx = e.touches[0].clientX },
        end(e) { if (this.sx === null) return; const dx = e.changedTouches[0].clientX - this.sx; if (Math.abs(dx) > 40) this.go(dx < 0 ? 1 : -1); this.sx = null },
     }"
     x-on:keydown.arrow-right.window="show && go(1)" x-on:keydown.arrow-left.window="show && go(-1)"
     x-trap.noscroll="show">
    <div class="flex items-center justify-between px-4 py-4 text-xs tracking-[0.2em]">
        <span class="tc-num opacity-70" x-text="(index + 1) + ' / ' + photos.length"></span>
        <button type="button" x-on:click="show = false" class="rounded-full bg-white/10 px-4 py-2 font-semibold tracking-normal transition hover:bg-white/20">Tutup</button>
    </div>
    <div class="relative flex flex-1 items-center justify-center px-4 pb-6" x-on:click.self="show = false" x-on:touchstart.passive="start($event)" x-on:touchend.passive="end($event)">
        <template x-for="(src, i) in photos" :key="i">
            <img x-show="i === index" x-bind:src="src" alt=""
                 x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 scale-[0.98]"
                 class="absolute max-h-[80vh] max-w-[calc(100%-2rem)] rounded-lg object-contain">
        </template>
        <button type="button" x-on:click="go(-1)" class="absolute left-3 hidden h-11 w-11 items-center justify-center rounded-full bg-white/10 transition hover:bg-white/20 sm:flex" aria-label="Foto sebelumnya">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
        </button>
        <button type="button" x-on:click="go(1)" class="absolute right-3 hidden h-11 w-11 items-center justify-center rounded-full bg-white/10 transition hover:bg-white/20 sm:flex" aria-label="Foto berikutnya">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/></svg>
        </button>
    </div>
    <p class="pb-6 text-center text-[11px] tracking-[0.2em] uppercase opacity-50 sm:hidden">Geser untuk foto lain</p>
</div>

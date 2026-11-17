{{-- Closing CTA: the bell rings once when it scrolls into view (and again on hover). --}}
<section class="bell-trigger relative overflow-hidden bg-rose-brand-50 py-24 sm:py-32">
    <div class="mx-auto max-w-4xl px-4 text-center sm:px-6" data-reveal>
        <x-brand.mark class="bell mx-auto h-14 w-14 text-rose-brand-500"/>
        <h2 class="display mt-8 text-[clamp(2.75rem,7vw,6.5rem)] text-ink">Siap membagikan <em>kabar bahagia?</em></h2>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg btn-arrow mt-12">
            Buat Undangan Gratis
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
        </a>
    </div>
</section>

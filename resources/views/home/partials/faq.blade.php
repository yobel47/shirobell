{{-- FAQ: heading and support link on the left, questions on the right. --}}
<section id="faq" class="scroll-mt-20 py-20 sm:py-28">
    <div class="mx-auto grid max-w-6xl gap-12 px-4 sm:px-6 lg:grid-cols-12 lg:gap-8">
        <div class="lg:col-span-4" data-reveal>
            <div class="lg:sticky lg:top-28">
                <p class="eyebrow">FAQ</p>
                <h2 class="display mt-6 text-[clamp(2.5rem,5vw,4.5rem)] text-ink">Pertanyaan <em>umum.</em></h2>
                @if ($contacts['whatsapp_url'])
                    <p class="mt-6 text-gray-600">
                        Masih ragu?
                        <a href="{{ $contacts['whatsapp_url'] }}" target="_blank" rel="noopener" class="font-medium text-rose-brand-600 underline decoration-rose-brand-300 underline-offset-4 hover:decoration-rose-brand-600">Tanya kami di WhatsApp</a>
                    </p>
                @endif
            </div>
        </div>

        <div class="border-b border-rose-brand-900/10 lg:col-span-7 lg:col-start-6">
            @foreach ($faqs as [$question, $answer])
                <details class="group border-t border-rose-brand-900/10" data-reveal style="--d: {{ $loop->index * 60 }}ms">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-6 py-6 text-lg font-medium tracking-[-0.01em] text-ink transition-colors hover:text-rose-brand-600 [&::-webkit-details-marker]:hidden">
                        {{ $question }}
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-rose-brand-900/15 text-rose-brand-500 transition duration-300 group-open:rotate-45 group-open:border-rose-brand-400" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        </span>
                    </summary>
                    <p class="max-w-xl pb-7 leading-relaxed text-gray-600">{{ $answer }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

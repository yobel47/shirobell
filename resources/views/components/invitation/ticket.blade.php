@props(['qr', 'guest'])
{{-- Guest entry ticket (ADR 011). Takes each theme's colors through the craft.css --tc-* tokens. --}}
<section id="tiket" class="px-6 py-14">
    <div class="relative mx-auto max-w-xs overflow-hidden rounded-3xl border border-dashed border-(--tc-accent)/50 bg-(--tc-surface) p-6 text-center text-(--tc-ink) shadow-sm">
        {{-- Ticket notches on both sides --}}
        <span class="absolute top-1/2 -left-3 h-6 w-6 -translate-y-1/2 rounded-full bg-(--tc-bg)" aria-hidden="true"></span>
        <span class="absolute top-1/2 -right-3 h-6 w-6 -translate-y-1/2 rounded-full bg-(--tc-bg)" aria-hidden="true"></span>
        <p class="tc-eyebrow">Tiket Masuk</p>
        <p class="tc-title mt-3 text-lg leading-tight font-semibold text-(--tc-primary)">{{ $guest->name }}</p>
        @if ($guest->group_name)
            <p class="mt-1 text-xs opacity-70">{{ $guest->group_name }}</p>
        @endif
        <div class="mx-auto mt-5 w-48 rounded-2xl bg-white p-3 [&_svg]:h-auto [&_svg]:w-full">{!! $qr !!}</div>
        <p class="tc-num mt-3 font-mono text-xs tracking-[0.3em] opacity-60">{{ strtoupper($guest->code) }}</p>
        <p class="tc-pretty mt-4 text-xs leading-relaxed opacity-75">Tunjukkan QR ini kepada penerima tamu saat tiba di lokasi acara.</p>
    </div>
</section>

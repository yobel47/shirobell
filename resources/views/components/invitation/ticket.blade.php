@props(['qr', 'guest'])
{{-- Guest entry ticket (ADR 011). Neutral styling so it fits every theme. --}}
<section id="tiket" class="px-6 py-12">
    <div class="mx-auto max-w-xs rounded-3xl border-2 border-dashed border-black/15 bg-white p-6 text-center text-gray-900 shadow-sm">
        <p class="text-[11px] font-semibold tracking-[0.3em] text-gray-500 uppercase">Tiket Masuk</p>
        <p class="mt-2 text-lg leading-tight font-semibold">{{ $guest->name }}</p>
        @if ($guest->group_name)
            <p class="text-xs text-gray-500">{{ $guest->group_name }}</p>
        @endif
        <div class="mx-auto mt-4 w-48 [&_svg]:h-auto [&_svg]:w-full">{!! $qr !!}</div>
        <p class="mt-2 font-mono text-xs tracking-widest text-gray-400">{{ strtoupper($guest->code) }}</p>
        <p class="mt-3 text-xs leading-relaxed text-gray-500">Tunjukkan QR ini kepada penerima tamu saat tiba di lokasi acara.</p>
    </div>
</section>

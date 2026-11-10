{{-- Printable QR cards, one per guest (A4, three per row). --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Kartu QR - {{ $invitation->coupleName() }}</title>
    @fonts
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans text-ink print:bg-white">
    <div class="sticky top-0 z-10 flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 bg-white px-4 py-3 print:hidden"
         x-data>
        <p class="text-sm"><strong>{{ $guests->count() }}</strong> kartu tamu &middot; {{ $invitation->coupleName() }}. Gunakan kertas A4; potong mengikuti garis putus-putus.</p>
        <button type="button" x-on:click="window.print()" class="btn btn-primary px-4 py-1.5">Cetak</button>
    </div>
    <main class="mx-auto grid max-w-[190mm] grid-cols-3 gap-[4mm] p-[4mm] print:max-w-none print:p-0">
        @forelse ($guests as $guest)
            <div class="flex break-inside-avoid flex-col items-center rounded-lg border border-dashed border-gray-300 bg-white p-[4mm] text-center">
                <p class="text-[8pt] tracking-[0.2em] text-gray-500 uppercase">The Wedding of</p>
                <p class="font-serif text-[11pt] font-semibold">{{ $invitation->coupleName() }}</p>
                <div class="my-[2mm] w-[32mm] [&_svg]:h-auto [&_svg]:w-full">{!! $qr($guest->code) !!}</div>
                <p class="text-[10pt] leading-tight font-semibold">{{ $guest->name }}</p>
                @if ($guest->group_name)
                    <p class="text-[8pt] text-gray-500">{{ $guest->group_name }}</p>
                @endif
                <p class="mt-[1mm] font-mono text-[7pt] text-gray-400">{{ strtoupper($guest->code) }}</p>
            </div>
        @empty
            <p class="col-span-3 py-10 text-center text-sm text-gray-500">Belum ada tamu. Tambahkan tamu di halaman Tamu.</p>
        @endforelse
    </main>
    @livewireScripts
</body>
</html>

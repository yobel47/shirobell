<div class="space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="font-serif text-lg font-semibold">Riwayat versi</h2>
            <p class="max-w-xl text-sm text-gray-500">
                Salinan dibuat otomatis sebelum Anda mengubah undangan, paling sering setiap {{ $interval }} menit, dan {{ $max }} versi terakhir disimpan.
                Memulihkan versi lama tidak menghapus apa pun: kondisi sekarang disimpan dulu sebagai versi baru.
            </p>
        </div>
        <button type="button" wire:click="saveNow" class="btn btn-outline shrink-0">Simpan versi sekarang</button>
    </div>

    <div class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">
        Yang dipulihkan: teks, data mempelai, acara, cerita, rekening amplop, tema, warna, dan font.
        Galeri foto, foto sampul dan mempelai, musik, tamu, serta ucapan tidak ikut berubah.
    </div>

    <ul class="divide-y divide-gray-100 rounded-2xl border border-gray-100 bg-white">
        @forelse ($versions as $version)
            @php
                $snapshot = $version->snapshot;
                $summary = trim(($snapshot['invitation']['groom_nickname'] ?? '').' & '.($snapshot['invitation']['bride_nickname'] ?? ''), ' &');
            @endphp
            <li wire:key="v-{{ $version->id }}" class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                <div class="min-w-0">
                    <p class="font-semibold">{{ $version->created_at->translatedFormat('d M Y, H.i') }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $version->reasonLabel() }}
                        @if ($version->user) &middot; {{ $version->user->isAdmin() ? 'Admin' : $version->user->name }} @endif
                        &middot; {{ $summary ?: 'Tanpa nama' }}, {{ count($snapshot['events'] ?? []) }} acara, {{ count($snapshot['stories'] ?? []) }} cerita
                    </p>
                </div>
                <button type="button" wire:click="restore({{ $version->id }})"
                        wire:confirm="Pulihkan versi {{ $version->created_at->translatedFormat('d M Y, H.i') }}? Kondisi sekarang akan disimpan dulu sebagai versi baru."
                        class="btn btn-ghost px-4 py-1.5 text-rose-brand-700">Pulihkan</button>
            </li>
        @empty
            <li class="px-4 py-6 text-center text-sm text-gray-500">Belum ada versi. Versi pertama dibuat otomatis saat Anda mulai mengubah undangan.</li>
        @endforelse
    </ul>
</div>

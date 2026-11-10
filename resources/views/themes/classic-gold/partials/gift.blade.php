<section class="bg-cg-blush/50 px-6 py-16 text-center" x-data="{ zoom: null }" x-on:keydown.escape.window="zoom = null">
    <h2 class="font-script text-5xl text-cg-deep">Amplop Digital</h2>
    <p class="mx-auto mt-4 max-w-xs text-sm leading-relaxed text-cg-ink/80">Doa restu Anda adalah karunia terindah bagi kami. Bagi yang ingin memberikan tanda kasih, dapat melalui:</p>

    <div class="mt-8 space-y-4">
        @foreach ($giftAccounts as $account)
            <div class="rounded-[2rem] border border-cg-rose/20 bg-white px-6 py-6 shadow-sm" x-data="{ copied: false }">
                <p class="text-xs font-semibold tracking-[0.25em] text-cg-rose uppercase">{{ $account->provider }}</p>
                @if ($account->isQris() && $account->qrisUrl())
                    <button type="button" x-on:click="zoom = @js($account->qrisUrl())" class="mx-auto mt-4 block w-48 overflow-hidden rounded-2xl border border-cg-rose/20 bg-white p-2" aria-label="Perbesar QRIS">
                        <img src="{{ $account->qrisUrl() }}" alt="QRIS {{ $account->account_name }}" class="w-full" loading="lazy">
                    </button>
                    <p class="mt-2 text-xs text-cg-ink/60">Ketuk untuk memperbesar, lalu pindai dengan aplikasi pembayaran.</p>
                @elseif ($account->account_number)
                    <p class="mt-3 font-serif text-2xl font-semibold tracking-wider text-cg-deep">{{ $account->account_number }}</p>
                @endif
                <p class="mt-2 text-sm">a.n. {{ $account->account_name }}</p>
                @if ($account->account_number)
                    <button type="button" class="mt-4 rounded-full border border-cg-deep/40 px-5 py-2 text-xs font-semibold text-cg-deep hover:bg-cg-blush"
                            x-on:click="navigator.clipboard.writeText(@js($account->account_number)).then(() => { copied = true; setTimeout(() => copied = false, 1500) })">
                        <span x-show="!copied">Salin nomor</span><span x-show="copied" x-cloak>Tersalin</span>
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <div x-show="zoom" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex flex-col items-center justify-center gap-4 bg-black/85 p-6" x-on:click.self="zoom = null">
        <img x-bind:src="zoom" alt="QRIS" class="max-h-[75vh] w-full max-w-sm rounded-2xl bg-white p-3">
        <div class="flex gap-2">
            <a x-bind:href="zoom" target="_blank" rel="noopener" class="rounded-full bg-white px-5 py-2 text-xs font-semibold text-cg-deep">Buka gambar</a>
            <button type="button" x-on:click="zoom = null" class="rounded-full bg-white/15 px-5 py-2 text-xs font-semibold text-white">Tutup</button>
        </div>
    </div>
</section>

<section class="bg-sm-blush/50 px-6 py-20 text-center" x-data="{ zoom: null }" x-on:keydown.escape.window="zoom = null">
    <p class="tc-eyebrow">Tanda Kasih</p>
    <h2 class="tc-title mt-3 font-script text-5xl text-sm-deep">Amplop Digital</h2>
    <p class="tc-pretty mx-auto mt-5 max-w-xs text-sm leading-relaxed text-sm-ink/80">Doa restu Anda adalah karunia terindah bagi kami. Bagi yang ingin memberikan tanda kasih, dapat melalui:</p>

    <div class="mt-8 space-y-4">
        @foreach ($giftAccounts as $account)
            <div class="rounded-[2rem] border border-sm-rose/20 bg-white px-6 py-6 shadow-sm" x-data="{ copied: false }">
                <p class="tc-eyebrow">{{ $account->provider }}</p>
                @if ($account->isQris() && $account->qrisUrl())
                    <button type="button" x-on:click="zoom = @js($account->qrisUrl())" class="mx-auto mt-4 block w-48 overflow-hidden rounded-2xl border border-sm-rose/20 bg-white p-2" aria-label="Perbesar QRIS">
                        <img src="{{ $account->qrisUrl() }}" alt="QRIS {{ $account->account_name }}" class="w-full" loading="lazy">
                    </button>
                    <p class="mt-2 text-xs text-sm-ink/60">Ketuk untuk memperbesar, lalu pindai dengan aplikasi pembayaran.</p>
                @elseif ($account->account_number)
                    <p class="tc-num mt-4 font-serif text-2xl font-semibold tracking-[0.08em] text-sm-deep">{{ $account->account_number }}</p>
                @endif
                <p class="mt-2 text-sm">a.n. {{ $account->account_name }}</p>
                @if ($account->account_number)
                    <button type="button" class="tc-btn-outline mt-5" x-bind:class="copied && 'bg-sm-blush'"
                            x-on:click="navigator.clipboard.writeText(@js($account->account_number)).then(() => { copied = true; setTimeout(() => copied = false, 1800) })">
                        <span x-show="!copied" class="inline-flex items-center gap-2"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2"/><path stroke-linecap="round" d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>Salin nomor</span>
                        <span x-show="copied" x-cloak class="inline-flex items-center gap-2" role="status"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>Tersalin</span>
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <div x-show="zoom" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex flex-col items-center justify-center gap-4 bg-black/85 p-6" x-on:click.self="zoom = null">
        <img x-bind:src="zoom" alt="QRIS" class="max-h-[75vh] w-full max-w-sm rounded-2xl bg-white p-3">
        <div class="flex gap-2">
            <a x-bind:href="zoom" target="_blank" rel="noopener" class="rounded-full bg-white px-5 py-2 text-xs font-semibold text-sm-deep">Buka gambar</a>
            <button type="button" x-on:click="zoom = null" class="rounded-full bg-white/15 px-5 py-2 text-xs font-semibold text-white">Tutup</button>
        </div>
    </div>
</section>

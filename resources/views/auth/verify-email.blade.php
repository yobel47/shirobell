<x-layouts.auth title="Verifikasi Email" heading="Cek email Anda" subheading="Kami sudah mengirim link verifikasi ke {{ auth()->user()->email }}. Klik link tersebut untuk mulai membuat undangan.">
    @if (session('status') === 'verification-link-sent')
        <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            Link verifikasi baru sudah dikirim.
        </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-full sm:w-auto">Kirim ulang link</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-ghost w-full sm:w-auto">Keluar</button>
        </form>
    </div>
</x-layouts.auth>

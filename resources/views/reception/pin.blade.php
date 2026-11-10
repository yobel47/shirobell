<x-layouts.base title="Penerima Tamu">
    <x-slot:head><meta name="robots" content="noindex, nofollow"></x-slot:head>
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="card w-full max-w-sm text-center">
            <p class="text-xs font-semibold tracking-[0.25em] text-rose-brand-500 uppercase">Penerima Tamu</p>
            <h1 class="mt-2 font-serif text-2xl font-semibold">{{ $invitation->coupleName() }}</h1>
            <p class="mt-2 text-sm text-gray-500">Masukkan PIN 6 digit dari pemilik undangan.</p>
            <form method="POST" action="{{ route('reception.login', $token) }}" class="mt-6 space-y-4">
                @csrf
                <input name="pin" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required autofocus
                       class="form-input text-center text-2xl tracking-[0.5em]" aria-label="PIN">
                @error('pin') <p class="form-error">{{ $message }}</p> @enderror
                <button type="submit" class="btn btn-primary w-full">Masuk</button>
            </form>
        </div>
    </main>
</x-layouts.base>

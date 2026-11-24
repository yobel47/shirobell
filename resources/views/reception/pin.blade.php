<x-layouts.base title="Penerima Tamu" robots="noindex, nofollow">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="card w-full max-w-sm px-6 py-10 text-center sm:px-8">
            <x-brand.mark class="mx-auto h-9 w-9 text-rose-brand-400"/>
            <p class="mt-5 text-xs font-semibold tracking-[0.2em] text-rose-brand-600 uppercase">Penerima Tamu</p>
            <h1 class="mt-3 font-serif text-3xl font-medium">{{ $invitation->coupleName() }}</h1>
            <p class="mt-2 text-sm text-gray-500">Masukkan PIN 6 digit dari pemilik undangan.</p>
            <form method="POST" action="{{ route('reception.login', $token) }}" class="mt-6 space-y-4">
                @csrf
                <input name="pin" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required autofocus
                       class="form-input py-4 text-center font-display text-4xl tracking-[0.4em] [font-variant-numeric:lining-nums]" aria-label="PIN">
                @error('pin') <p class="form-error">{{ $message }}</p> @enderror
                <button type="submit" class="btn btn-primary btn-lg w-full">Masuk</button>
            </form>
        </div>
    </main>
</x-layouts.base>

<x-layouts.auth title="Lupa Kata Sandi" heading="Lupa kata sandi" subheading="Masukkan email Anda, kami kirimkan link untuk mengatur ulang kata sandi.">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <x-form.input name="email" type="email" label="Email" required autofocus autocomplete="email" />
        <button type="submit" class="btn btn-primary w-full">Kirim link</button>
    </form>

    <x-slot:footer>
        <p class="mt-6 text-sm text-gray-500"><a href="{{ route('login') }}" class="font-medium text-rose-brand-600 hover:underline">Kembali ke halaman masuk</a></p>
    </x-slot:footer>
</x-layouts.auth>

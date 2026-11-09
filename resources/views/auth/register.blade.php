<x-layouts.auth title="Daftar" heading="Buat akun" subheading="Gratis. Anda baru membayar saat undangan siap diterbitkan.">
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <x-form.input name="name" label="Nama" required autofocus autocomplete="name" />
        <x-form.input name="email" type="email" label="Email" required autocomplete="email" />
        <x-form.input name="phone" type="tel" label="Nomor WhatsApp (opsional)" placeholder="081234567890" autocomplete="tel" inputmode="tel" />
        <x-form.input name="password" type="password" label="Kata sandi" required autocomplete="new-password" hint="Minimal 8 karakter." />
        <x-form.input name="password_confirmation" type="password" label="Ulangi kata sandi" required autocomplete="new-password" />

        <button type="submit" class="btn btn-primary w-full">Daftar</button>
    </form>

    <x-slot:footer>
        <p class="mt-6 text-sm text-gray-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-rose-brand-600 hover:underline">Masuk</a></p>
    </x-slot:footer>
</x-layouts.auth>

<x-layouts.auth title="Masuk" heading="Selamat datang kembali" subheading="Masuk untuk mengelola undangan Anda.">
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <x-form.input name="email" type="email" label="Email" required autofocus autocomplete="email" />
        <x-form.input name="password" type="password" label="Kata sandi" required autocomplete="current-password" />

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-rose-brand-500 focus:ring-rose-brand-300">
                Ingat saya
            </label>
            <a href="{{ route('password.request') }}" class="font-medium text-rose-brand-600 hover:underline">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="btn btn-primary w-full">Masuk</button>
    </form>

    <x-slot:footer>
        <p class="mt-6 text-sm text-gray-500">Belum punya akun? <a href="{{ route('register') }}" class="font-medium text-rose-brand-600 hover:underline">Daftar gratis</a></p>
    </x-slot:footer>
</x-layouts.auth>

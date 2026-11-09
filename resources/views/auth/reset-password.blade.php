<x-layouts.auth title="Atur Ulang Kata Sandi" heading="Atur ulang kata sandi">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <x-form.input name="email" type="email" label="Email" :value="$request->email" required autocomplete="email" />
        <x-form.input name="password" type="password" label="Kata sandi baru" required autofocus autocomplete="new-password" />
        <x-form.input name="password_confirmation" type="password" label="Ulangi kata sandi baru" required autocomplete="new-password" />
        <button type="submit" class="btn btn-primary w-full">Simpan kata sandi</button>
    </form>
</x-layouts.auth>

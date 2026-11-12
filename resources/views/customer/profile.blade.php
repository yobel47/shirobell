<x-layouts.app title="Profil">
    <div class="mx-auto max-w-2xl space-y-6">
        <div>
            <h1 class="font-serif text-3xl font-semibold">Profil &amp; kata sandi</h1>
            <p class="mt-1 text-sm text-gray-500">Data ini dipakai untuk login, bukti pembayaran, dan pengingat masa aktif undangan.</p>
        </div>

        @php $user = auth()->user(); @endphp

        <form method="POST" action="{{ route('user-profile-information.update') }}" class="card space-y-4">
            @csrf
            @method('PUT')
            <h2 class="font-serif text-lg font-semibold">Data akun</h2>
            @php $profileErrors = $errors->getBag('updateProfileInformation'); @endphp
            <div>
                <label for="name" class="form-label">Nama</label>
                <input id="name" name="name" type="text" required maxlength="255" autocomplete="name" class="form-input" value="{{ old('name', $user->name) }}">
                @if ($profileErrors->has('name')) <p class="form-error">{{ $profileErrors->first('name') }}</p> @endif
            </div>
            <div>
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" required maxlength="255" autocomplete="email" class="form-input" value="{{ old('email', $user->email) }}">
                <p class="mt-1.5 text-xs text-gray-500">Jika email diganti, Anda perlu memverifikasi email baru sebelum bisa melanjutkan.</p>
                @if ($profileErrors->has('email')) <p class="form-error">{{ $profileErrors->first('email') }}</p> @endif
            </div>
            <div>
                <label for="phone" class="form-label">Nomor WhatsApp (opsional)</label>
                <input id="phone" name="phone" type="tel" inputmode="tel" maxlength="20" placeholder="081234567890" autocomplete="tel" class="form-input" value="{{ old('phone', $user->phone) }}">
                @if ($profileErrors->has('phone')) <p class="form-error">{{ $profileErrors->first('phone') }}</p> @endif
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">Simpan profil</button>
            </div>
        </form>

        <form method="POST" action="{{ route('user-password.update') }}" class="card space-y-4">
            @csrf
            @method('PUT')
            <h2 class="font-serif text-lg font-semibold">Ubah kata sandi</h2>
            @php $passwordErrors = $errors->getBag('updatePassword'); @endphp
            <div>
                <label for="current_password" class="form-label">Kata sandi saat ini</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="form-input">
                @if ($passwordErrors->has('current_password')) <p class="form-error">{{ $passwordErrors->first('current_password') }}</p> @endif
            </div>
            <div>
                <label for="password" class="form-label">Kata sandi baru</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="form-input">
                <p class="mt-1.5 text-xs text-gray-500">Minimal 8 karakter.</p>
                @if ($passwordErrors->has('password')) <p class="form-error">{{ $passwordErrors->first('password') }}</p> @endif
            </div>
            <div>
                <label for="password_confirmation" class="form-label">Ulangi kata sandi baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="form-input">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">Ubah kata sandi</button>
            </div>
        </form>

        <p class="text-sm text-gray-500">Lupa kata sandi? Keluar, lalu pilih "Lupa kata sandi" di halaman masuk.</p>
    </div>
</x-layouts.app>

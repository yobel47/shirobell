<x-mail::message>
# Masa aktif berakhir

Halo {{ $invitation->user->name }},

Masa aktif undangan **{{ $invitation->coupleName() }}** telah berakhir. Tamu sekarang melihat halaman "masa aktif berakhir". Data, foto, dan ucapan tetap tersimpan dan undangan bisa diaktifkan kembali kapan saja.

<x-mail::button :url="route('app.checkout', $invitation)">
Perpanjang undangan
</x-mail::button>

Salam hangat,<br>
{{ config('app.name') }}
</x-mail::message>

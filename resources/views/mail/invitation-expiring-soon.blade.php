<x-mail::message>
# Undangan segera berakhir

Halo {{ $invitation->user->name }},

Masa aktif undangan **{{ $invitation->coupleName() }}** akan berakhir pada **{{ $invitation->active_until?->translatedFormat('d F Y') }}**. Setelah itu undangan tidak bisa dibuka tamu, tetapi semua data tetap tersimpan.

<x-mail::button :url="route('app.checkout', $invitation)">
Perpanjang undangan
</x-mail::button>

Salam hangat,<br>
{{ config('app.name') }}
</x-mail::message>

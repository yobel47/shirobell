<x-mail::message>
# Pembayaran berhasil

Halo {{ $order->user->name }},

Terima kasih, pembayaran untuk undangan **{{ $invitation->coupleName() }}** sudah kami terima.

<x-mail::table>
| | |
|:--|--:|
| Nomor order | {{ $order->number }} |
| Jenis | {{ $order->typeLabel() }} |
| Total | Rp {{ number_format($order->total_amount, 0, ',', '.') }} |
| Aktif sampai | {{ $invitation->active_until?->translatedFormat('d F Y') }} |
</x-mail::table>

Undangan Anda sekarang bisa dibuka di:
{{ $invitation->publicUrl() }}

<x-mail::button :url="route('app.invitations.guests', $invitation)">
Bagikan ke tamu
</x-mail::button>

Salam hangat,<br>
{{ config('app.name') }}
</x-mail::message>

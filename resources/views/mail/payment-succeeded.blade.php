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

@if ($order->type === 'event_day')
Fitur Hari-H sudah aktif: aplikasi penerima tamu, tiket QR, cetak kartu QR, dan layar sapa.

<x-mail::button :url="route('app.invitations.event-day', $invitation)">
Buka fitur Hari-H
</x-mail::button>
@else
Undangan Anda sekarang bisa dibuka di:
{{ $invitation->publicUrl() }}

<x-mail::button :url="route('app.invitations.guests', $invitation)">
Bagikan ke tamu
</x-mail::button>
@endif

Salam hangat,<br>
{{ config('app.name') }}
</x-mail::message>

<ul>
    @if ($legal['email'])
        <li>Email: <a href="mailto:{{ $legal['email'] }}">{{ $legal['email'] }}</a></li>
    @endif
    @if ($legal['whatsapp_url'])
        <li>WhatsApp: <a href="{{ $legal['whatsapp_url'] }}" target="_blank" rel="noopener">{{ $legal['whatsapp'] }}</a></li>
    @endif
    @if ($legal['instagram_url'])
        <li>Instagram: <a href="{{ $legal['instagram_url'] }}" target="_blank" rel="noopener">{{ '@'.$legal['instagram'] }}</a></li>
    @endif
    @if ($legal['address'])
        <li>Alamat: {{ $legal['business'] }}, {{ $legal['address'] }}</li>
    @endif
    @if (! $legal['email'] && ! $legal['whatsapp_url'] && ! $legal['instagram_url'])
        <li>Kontak support belum tersedia. Silakan kembali lagi nanti.</li>
    @endif
</ul>

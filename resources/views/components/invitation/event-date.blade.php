@props(['date'])
{{-- Editorial event date shared by every theme: weekday | big day | month year. Colors come from --tc-* tokens. --}}
<div {{ $attributes->merge(['class' => 'tc-date']) }}>
    <span class="tc-date-side text-right">{{ $date->translatedFormat('l') }}</span>
    <span class="tc-date-day">{{ $date->format('d') }}</span>
    <span class="tc-date-side text-left tc-num">{{ $date->translatedFormat('M Y') }}</span>
</div>
<p class="sr-only">{{ $date->translatedFormat('l, d F Y') }}</p>

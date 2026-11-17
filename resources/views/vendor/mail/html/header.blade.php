@props(['url'])
{{-- Serif wordmark, "bell" in rose (email clients without Cormorant fall back to Georgia). --}}
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (strcasecmp(trim($slot), 'Shirobell') === 0)
Shiro<span class="wordmark-accent">bell</span>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>

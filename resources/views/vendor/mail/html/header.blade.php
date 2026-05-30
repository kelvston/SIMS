@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<span style="font-size: 19px; font-weight: bold;">{{ config('app.name', 'Laravel') }}</span>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>

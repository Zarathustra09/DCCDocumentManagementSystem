@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ $message->embed(public_path('logo.png')) }}" class="logo" alt="{{ config('app.name') }}" style="display: block; height: auto; max-height: 80px; width: auto; max-width: 200px;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>

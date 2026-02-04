@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ asset('logo.png') }}" class="logo" alt="{{ config('app.name') }}" style="display: block; height: auto; max-height: 80px; width: auto; max-width: 200px;">
</a>
</td>
</tr>

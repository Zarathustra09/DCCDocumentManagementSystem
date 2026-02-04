@props(['url'])
@php
    $logoPath = public_path('logo.png');
    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    $mimeType = file_exists($logoPath) ? mime_content_type($logoPath) : 'image/png';
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if($logoData)
<img src="data:{{ $mimeType }};base64,{{ $logoData }}" class="logo" alt="{{ config('app.name') }}" style="display: block; height: auto; max-height: 80px; width: auto; max-width: 200px;">
@else
{{ config('app.name') }}
@endif
</a>
</td>
</tr>

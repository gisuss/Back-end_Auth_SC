<tr>
<td class="header">
{{-- <a href="{{ $url }}" style="display: inline-block;"> --}}
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ asset('img/logo/logo_facyt.png') }}" class="logo" alt="SGSC Logo">
@endif
</a>
</td>
</tr>
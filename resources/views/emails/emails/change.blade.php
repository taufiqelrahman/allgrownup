@component('mail::message')
# Hello!

You are receiving this email because we received an email change request for your account.

@component('mail::button', ['url' => $url])
Confirm
@endcomponent

If you did not request an email change, no further action is required.
<br><br>

Thanks,
<br>
{{ config('app.name') }}
@endcomponent

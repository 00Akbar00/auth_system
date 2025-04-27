@component('mail::message')
# Verify Your Email Address

Please click the button below to verify your email address.

@component('mail::button', ['url' => $url])
Verify Email Address
@endcomponent

This link will expire in 24 hours. If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
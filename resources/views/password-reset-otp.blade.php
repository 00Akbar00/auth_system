@component('mail::message')
# Password Reset OTP

Your password reset OTP is: **{{ $otp }}**

This OTP was requested from IP address: {{ $ipAddress }}

The OTP will expire in 15 minutes. If you didn't request this, please secure your account.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
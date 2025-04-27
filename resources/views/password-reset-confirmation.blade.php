@component('mail::message')
# Password Reset Successful

Your password has been successfully reset. If you didn't make this change, please contact us immediately.

For security, all active sessions were terminated when you reset your password.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
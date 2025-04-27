<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordResetRequest extends FormRequest
{
public function rules()
{
$rules = [
'email' => 'required|email|exists:users,email',
];

// For the reset password form
if ($this->isMethod('post') && $this->routeIs('password.update')) {
$rules = [
'email' => 'required|email|exists:users,email',
'otp' => 'required|string|size:6',
'password' => [
'required',
'string',
'confirmed',
Password::min(12)
->letters()
->mixedCase()
->numbers()
->symbols()
->uncompromised(),
],
];
}

return $rules;
}

public function messages()
{
return [
'email.exists' => 'If your email is registered, you will receive a password reset OTP',
];
}
}
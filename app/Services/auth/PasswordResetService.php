<?php
namespace App\Services\Auth;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetEmail;

class PasswordResetService
{
public function requestReset(string $email, string $ip): void
{
$user = User::where('email', $email)->first();

if (!$user || $user->status !== 'verified') {
return; // Don't reveal if user exists
}

// Delete any existing reset tokens
PasswordReset::where('userId', $user->id)->delete();

// Generate OTP
$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$hashedOtp = Hash::make($otp);

// Store reset request
PasswordReset::create([
'userId' => $user->id,
'otpHash' => $hashedOtp,
'ipAddress' => $ip,
'expiresAt' => Carbon::now()->addMinutes(15),
]);

// Send email
Mail::to($user->email)->send(new PasswordResetEmail($otp, $ip));
}

public function resetPassword(string $email, string $otp, string $newPassword): User
{
$user = User::where('email', $email)->firstOrFail();

$resetRequest = PasswordReset::where('userId', $user->id)
->where('expiresAt', '>', now())
->firstOrFail();

if (!Hash::check($otp, $resetRequest->otpHash)) {
throw new \Exception('Invalid OTP');
}

// Update password
$user->passwordHash = Hash::make($newPassword);
$user->save();

// Delete the reset request
$resetRequest->delete();

// Invalidate all sessions
$user->sessions()->delete();

return $user;
}
}
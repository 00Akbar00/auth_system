<?php
namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserVerification;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VerificationService
{
public function verifyEmail(string $token): User
{
$verification = UserVerification::where('verificationToken', $token)
->where('expiresAt', '>', now())
->firstOrFail();

$user = $verification->user;

if ($user->status === 'verified') {
throw new \Exception('Email already verified');
}

$user->status = 'verified';
$user->save();

// Delete the verification record
$verification->delete();

return $user;
}

public function resendVerificationEmail(User $user): void
{
if ($user->status === 'verified') {
throw new \Exception('Email already verified');
}

// Delete any existing verification tokens
$user->verifications()->delete();

$verification = UserVerification::create([
'userId' => $user->id,
'verificationToken' => Str::uuid(),
'expiresAt' => Carbon::now()->addHours(24),
]);

Mail::to($user->email)->send(new VerificationEmail($verification));
}

public function createVerification(User $user): void
{
$verification = UserVerification::create([
'userId' => $user->id,
'verificationToken' => Str::uuid(),
'expiresAt' => Carbon::now()->addHours(24),
]);

Mail::to($user->email)->send(new VerificationEmail($verification));
}
}
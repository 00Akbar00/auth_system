<?php

namespace App\Services;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetService
{
    public function createPasswordResetOtp(User $user, string $ipAddress): array
    {
        // Delete any existing OTPs for this user
        $user->passwordResets()->delete();

        $otp = Str::padLeft(rand(0, 999999), 6, '0');
        $hashedOtp = Hash::make($otp);

        $passwordReset = $user->passwordResets()->create([
            'otpHash' => $hashedOtp,
            'ipAddress' => $ipAddress,
            'expiresAt' => Carbon::now()->addMinutes(15),
        ]);

        return [
            'otp' => $otp,
            'passwordReset' => $passwordReset,
        ];
    }

    public function validateOtp(User $user, string $otp, string $ipAddress): void
    {
        $passwordReset = $user->passwordResets()
            ->where('expiresAt', '>', now())
            ->where('ipAddress', $ipAddress)
            ->first();

        if (!$passwordReset || !Hash::check($otp, $passwordReset->otpHash)) {
            throw new \Exception('Invalid or expired OTP.');
        }

        $passwordReset->delete();
    }

    public function sendResetOtpEmail(User $user, string $otp, string $ipAddress): void
    {
        // Implement your email sending logic here
        // Example:
        // Mail::to($user->email)->send(new PasswordResetOtpMail($otp, $ipAddress));
    }

    public function sendPasswordResetConfirmation(User $user): void
    {
        // Implement your email sending logic here
        // Example:
        // Mail::to($user->email)->send(new PasswordResetConfirmationMail());
    }
}

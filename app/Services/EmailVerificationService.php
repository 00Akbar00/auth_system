<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailVerificationService
{
    public function createVerification(User $user): array
    {
        // Delete any existing verification tokens
        $user->verifications()->delete();

        $token = Str::uuid()->toString();
        $hashedToken = Hash::make($token);

        $verification = $user->verifications()->create([
            'verificationToken' => $hashedToken,
            'expiresAt' => Carbon::now()->addHours(24),
        ]);

        return [
            'token' => $token,
            'verification' => $verification,
        ];
    }

    public function verifyToken(string $hashedUserId, string $token): User
    {
        // Verify the hashed user ID (you might want to implement proper hashing/verification)
        $userId = $this->decodeHashedUserId($hashedUserId);

        $user = User::findOrFail($userId);

        if ($user->status === 'verified') {
            throw new \Exception('Email already verified.');
        }

        $verification = $user->verifications()
            ->where('expiresAt', '>', now())
            ->first();

        if (!$verification || !Hash::check($token, $verification->verificationToken)) {
            throw new \Exception('Invalid or expired verification token.');
        }

        $user->update(['status' => 'verified']);
        $verification->delete();

        return $user;
    }

    public function sendVerificationEmail(User $user, string $token): void
    {
        // Implement your email sending logic here
        $hashedUserId = $this->hashUserId($user->id);
        $verificationUrl = route('verification.verify', [
            'hashedUserId' => $hashedUserId,
            'token' => $token,
        ]);

        // Example using Laravel mail (you'll need to create the mailable)
        // Mail::to($user->email)->send(new EmailVerificationMail($verificationUrl));
    }

    protected function hashUserId(int $userId): string
    {
        // Implement a secure way to hash the user ID for the URL
        return hash_hmac('sha256', $userId, config('app.key'));
    }

    protected function decodeHashedUserId(string $hashedUserId): int
    {
        // Implement decoding logic
        // This is a simplified example - in production, you'd need a proper way to map hashes to IDs
        $user = User::whereRaw("SHA2(CONCAT(id, ?), 256) = ?", [config('app.key'), $hashedUserId])
            ->first();

        if (!$user) {
            throw new \Exception('Invalid user ID hash.');
        }

        return $user->id;
    }
}

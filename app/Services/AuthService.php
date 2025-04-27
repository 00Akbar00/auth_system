<?php

namespace App\Services;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function createUser(string $email, string $password): User
    {
        if (User::where('email', $email)->exists()) {
            throw new \Exception('Email already in use.');
        }

        return User::create([
            'email' => $email,
            'passwordHash' => Hash::make($password),
            'status' => 'unverified',
        ]);
    }

    public function attemptLogin(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->passwordHash)) {
            throw new \Exception('Invalid credentials.');
        }

        return $user;
    }

    public function findVerifiedUserByEmail(string $email): User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('User not found.');
        }

        if ($user->status !== 'verified') {
            throw new \Exception('Account not verified.');
        }

        return $user;
    }

    public function generateApiToken(User $user): PersonalAccessToken
    {
        $token = Str::random(64);

        return $user->tokens()->create([
            'tokenHash' => hash('sha256', $token),
            'expiresAt' => now()->addDays(30),
        ]);
    }

    public function revokeAllApiTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function invalidateAllSessions(User $user): void
    {
        $user->sessions()->delete();
    }

    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update(['passwordHash' => Hash::make($newPassword)]);
    }
}

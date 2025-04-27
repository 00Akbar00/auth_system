<?php

namespace App\Services;

use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Str;

class SessionService
{
    public function createSession(User $user, string $ipAddress, string $userAgent): Session
    {
        $sessionId = Str::random(64);

        return $user->sessions()->create([
            'sessionId' => hash('sha256', $sessionId),
            'ipAddress' => $ipAddress,
            'userAgent' => $userAgent,
            'metadata' => json_encode([
                'created_at' => now()->toDateTimeString(),
            ]),
            'expiresAt' => now()->addDays(30),
        ]);
    }

    public function destroySession(User $user, string $sessionId): void
    {
        $user->sessions()
            ->where('sessionId', hash('sha256', $sessionId))
            ->delete();
    }

    public function validateSession(string $sessionId): bool
    {
        return Session::where('sessionId', hash('sha256', $sessionId))
            ->where('expiresAt', '>', now())
            ->exists();
    }
}

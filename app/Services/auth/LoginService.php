<?php
namespace App\Services\Auth;

use App\Models\User;
use App\Models\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Carbon;

class LoginService
{
    public function attemptLogin(array $credentials, string $ip, string $userAgent): array
    {
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($credentials['password'], $user->passwordHash)) {
            RateLimiter::hit($this->throttleKey($credentials['email']));
            throw new \Exception('Invalid credentials');
        }

        // Check if email is verified
        if ($user->status !== 'verified') {
            throw new \Exception('Account not verified. Please check your email from registration.');
        }

        // Check for suspicious activity (simple rate limiting)
        if (RateLimiter::tooManyAttempts($this->throttleKey($credentials['email']), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($credentials['email']));
            throw new \Exception("Too many login attempts. Please try again in {$seconds} seconds.");
        }

        // Create new session
        $session = $this->createSession($user, $ip, $userAgent);

        return [
            'user' => $user,
            'session' => $session,
        ];
    }

    protected function createSession(User $user, string $ip, string $userAgent): Session
    {
        // Invalidate any existing sessions if needed
        // $user->sessions()->delete();

        return Session::create([
            'userId' => $user->id,
            'sessionId' => Str::random(64),
            'ipAddress' => $ip,
            'userAgent' => $userAgent,
            'expiresAt' => Carbon::now()->addDays(30),
        ]);
    }

    protected function throttleKey(string $email): string
    {
        return 'login_attempts:' . strtolower($email);
    }
}
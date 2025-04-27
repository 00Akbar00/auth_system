<?php
namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserVerification;
use App\Mail\WelcomeEmail;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegistrationService
{
    public function registerUser(array $data): User
    {
        $user = User::create([
            'email' => $data['email'],
            'name' => $data['name'],
            'passwordHash' => Hash::make($data['password']),
            'status' => 'unverified',
        ]);

        $this->createVerification($user);
        $this->sendWelcomeEmail($user);

        return $user;
    }

    protected function createVerification(User $user): void
    {
        $verification = UserVerification::create([
            'userId' => $user->id,
            'verificationToken' => Str::uuid(),
            'expiresAt' => now()->addHours(24),
        ]);

        Mail::to($user->email)->send(new VerificationEmail($verification));
    }

    protected function sendWelcomeEmail(User $user): void
    {
        Mail::to($user->email)->send(new WelcomeEmail($user));
    }
}
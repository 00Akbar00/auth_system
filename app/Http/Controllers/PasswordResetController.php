<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    protected $passwordResetService;
    protected $authService;

    public function __construct(
        PasswordResetService $passwordResetService,
        AuthService $authService
    ) {
        $this->passwordResetService = $passwordResetService;
        $this->authService = $authService;
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $user = $this->authService->findVerifiedUserByEmail($request->email);

            $otpData = $this->passwordResetService->createPasswordResetOtp(
                $user,
                $request->ip()
            );

            // Send OTP email (implement this)
            $this->passwordResetService->sendResetOtpEmail($user, $otpData['otp'], $request->ip());

            return response()->json([
                'message' => 'OTP sent to your email address.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Password reset failed.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => [
                'required',
                'string',
                Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        try {
            $user = $this->authService->findVerifiedUserByEmail($request->email);

            $this->passwordResetService->validateOtp(
                $user,
                $request->otp,
                $request->ip()
            );

            // Update password
            $this->authService->updatePassword($user, $request->password);

            // Invalidate all sessions
            $this->authService->invalidateAllSessions($user);

            // Optionally invalidate API tokens
            $this->authService->revokeAllApiTokens($user);

            // Send confirmation email
            $this->passwordResetService->sendPasswordResetConfirmation($user);

            return response()->json([
                'message' => 'Password reset successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Password reset failed.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\EmailVerificationService;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected $authService;
    protected $emailVerificationService;
    protected $sessionService;

    public function __construct(
        AuthService $authService,
        EmailVerificationService $emailVerificationService,
        SessionService $sessionService
    ) {
        $this->authService = $authService;
        $this->emailVerificationService = $emailVerificationService;
        $this->sessionService = $sessionService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
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
            $user = $this->authService->createUser(
                $request->email,
                $request->password
            );

            $verificationData = $this->emailVerificationService->createVerification($user);

            // Send verification email (you'll need to implement this)
            $this->emailVerificationService->sendVerificationEmail($user, $verificationData['token']);

            return response()->json([
                'message' => 'Registration successful. Please check your email for verification.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function verifyEmail($hashedUserId, $token)
    {
        try {
            $user = $this->emailVerificationService->verifyToken($hashedUserId, $token);

            // Create session and log user in
            $session = $this->sessionService->createSession($user, request()->ip(), request()->userAgent());

            // Generate API token if needed
            $apiToken = $this->authService->generateApiToken($user);

            return response()->json([
                'message' => 'Email verified successfully.',
                'user' => $user,
                'session_id' => $session->sessionId,
                'api_token' => $apiToken->plainTextToken ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Email verification failed.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = $this->authService->attemptLogin(
                $request->email,
                $request->password
            );

            if ($user->status !== 'verified') {
                return response()->json([
                    'message' => 'Account not verified. Please check your email from registration.',
                ], 403);
            }

            // Create session
            $session = $this->sessionService->createSession($user, request()->ip(), request()->userAgent());

            // Generate API token if requested
            $apiToken = $request->boolean('request_api_token')
                ? $this->authService->generateApiToken($user)
                : null;

            return response()->json([
                'message' => 'Login successful.',
                'user' => $user,
                'session_id' => $session->sessionId,
                'api_token' => $apiToken->plainTextToken ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed.',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->sessionService->destroySession($request->user(), $request->session_id);

            // Optionally revoke API tokens
            if ($request->boolean('revoke_api_tokens')) {
                $this->authService->revokeAllApiTokens($request->user());
            }

            return response()->json(['message' => 'Logged out successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegistrationService;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    protected RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->registrationService->registerUser($request->validated());

        return response()->json([
            'message' => 'User registered successfully. Please check your email for verification.',
            'user' => $user->only(['email', 'name', 'createdAt']),
        ], 201);
    }
}
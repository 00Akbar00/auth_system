<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\JsonResponse;


class PasswordResetController extends Controller
{
protected PasswordResetService $passwordResetService;

public function __construct(PasswordResetService $passwordResetService)
{
$this->passwordResetService = $passwordResetService;
}

public function sendResetLinkEmail(PasswordResetRequest $request): JsonResponse
{
$this->passwordResetService->requestReset(
$request->email,
$request->ip()
);

return response()->json([
'message' => 'If your email is registered, you will receive a password reset OTP'
]);
}

public function reset(PasswordResetRequest $request): JsonResponse
{
try {
$user = $this->passwordResetService->resetPassword(
$request->email,
$request->otp,
$request->password
);

return response()->json([
'message' => 'Password reset successfully. Please login with your new password.'
]);

} catch (\Exception $e) {
return response()->json([
'message' => $e->getMessage()
], 400);
}
}
}
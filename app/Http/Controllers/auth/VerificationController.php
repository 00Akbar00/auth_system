<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserVerification;
use App\Services\Auth\VerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
protected VerificationService $verificationService;

public function __construct(VerificationService $verificationService)
{
$this->verificationService = $verificationService;
}

public function show(Request $request)
{
return view('auth.verify');
}

public function verify(Request $request, string $token): JsonResponse
{
try {
$user = $this->verificationService->verifyEmail($token);

return response()->json([
'message' => 'Email verified successfully',
'user' => $user
]);

} catch (\Exception $e) {
return response()->json([
'message' => $e->getMessage()
], 400);
}
}

public function resend(Request $request): JsonResponse
{
try {
$this->verificationService->resendVerificationEmail($request->user());

return response()->json([
'message' => 'Verification email resent successfully'
]);

} catch (\Exception $e) {
return response()->json([
'message' => $e->getMessage()
], 400);
}
}
}
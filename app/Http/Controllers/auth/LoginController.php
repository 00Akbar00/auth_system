<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
class LoginController extends Controller
{
protected LoginService $loginService;

public function __construct(LoginService $loginService)
{
$this->loginService = $loginService;
}

public function login(LoginRequest $request): JsonResponse
{
try {
$result = $this->loginService->attemptLogin(
$request->validated(),
$request->ip(),
$request->userAgent()
);

// Set session cookie
return response()->json([
'message' => 'Login successful',
'user' => $result['user'],
'session_token' => $result['session']->sessionId,
])->cookie(
'session_token',
$result['session']->sessionId,
config('session.lifetime'),
'/',
null,
true,
true
);

} catch (\Exception $e) {
return response()->json([
'message' => $e->getMessage(),
], 401);
}
}

public function logout(): JsonResponse
{
// Get session from cookie
$sessionId = request()->cookie('session_token');

if ($sessionId) {
Session::where('sessionId', $sessionId)->delete();
}

return response()->json([
'message' => 'Logged out successfully'
])->withoutCookie('session_token');
}
}
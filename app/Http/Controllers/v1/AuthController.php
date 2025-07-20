<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        // Validate the incoming request
        $validateRequest = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:5',
        ]);
        if($validateRequest->fails()){
            return self::error(__('messages.validation_error'), 422, $validateRequest->getMessageBag()->toArray());
        }

        $credentials = $request->only('email', 'password');

        // Attempt to authenticate and get a token
        if(!$token = auth()->attempt(array_merge($credentials, ['is_active' => 1]))) {
            return self::error(__('messages.invalid_credentials'), 401);
        }else{
            $user = auth()->user();
            $path = $request->path(); // e.g., "api/v1/admin/login" or "api/v1/login"

            if (Str::contains($path, '/admin/')) {
                // This is an admin login attempt
                if (!$user->is_sys_user) {
//                    auth()->logout();
                    return self::error(__('messages.admin_only'), 403);
                }
            }
        }

        // Generate refresh token from access token
        $refreshToken = auth()->refresh();

        // Get authenticated user and filter required attributes
        $user = auth()->user();
        $userData = $user->only(['id', 'first_name', 'last_name', 'display_name', 'phone', 'email']);

        // Get the first role of the user (assuming a user has at least one role)
        $role = $user->getRoleNames()->first(); // Returns a single role name

        // Determine redirect URL based on role
        if ($user->hasRole('sysadmin') || $user->hasRole('sysuser')) {
            $redirectUrl = '/admin/dashboard';
        } else {
            $redirectUrl = '/user/dashboard';
        }

        // Respond with the token
        return $this->respondWithToken($token, $refreshToken, array_merge($userData, ['redirect_url' => $redirectUrl, 'role' => $role]));
    }

    public function refresh(): \Illuminate\Http\JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function respondWithToken($token, $refresh_token = null, $user=null): \Illuminate\Http\JsonResponse
    {
        $data = [
            'token' => [
                'accessToken' => $token,
                'refreshToken' => auth()->setTTL(config('jwt.refresh_ttl'))->refresh(),
                'type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60, // Token expiration time in seconds
            ],
            'user' => $user,
        ];
        return self::success($data, __('messages.login_successfully'));
    }

    public function logout()
    {
        auth()->logout();

        return self::success([],__('messages.successfully_logged_out'), 200);
    }
}

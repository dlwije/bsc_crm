<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        // Define the base validation rules
        $validationRules = [
            'userName' => 'required|string|max:255',
            'password' => 'required|string|confirmed|min:5',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $validationRules);

        // Check if validation fails
        if ($validator->fails()) {
            return self::error(__('messages.validation_error'), 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            // Prepare user data
            $userData = [
                'name' => $request->input('name') ?? null,
                'userName' => $request->input('userName'),
                'Department' => $request->input('Department') ?? null,
                'branch' => $request->input('branch') ?? null,
                'password' => bcrypt($request->input('password')), // Hash the password
                'last_ip' => $request->ip(),
                'login_time' => now(),
                'last_activity' => now(),
                'status' => 'sysuser',
            ];

            // Create the user
            $user = User::create($userData);
            $user->syncRoles(2);  // Assign role to user

            // Generate JWT access token
            $token = JWTAuth::attempt($request->only('userName', 'password'));

            // Create refresh token directly from the generated JWT
            // Note: You don't need to pass the access token to refresh here.
            $refreshToken = $token;  // Use the access token as the refresh token

            // Prepare the response data
            $data = [
                'token' => [
                    'accessToken' => $token,
                    'refreshToken' => $refreshToken,
                    'type' => 'bearer',
                    'expires_in' => (int) config('jwt.ttl', 60) * 60, // Access token expiration in seconds
                    'refresh_expires_in' => (int) config('jwt.ttl', 60) * 60, // Refresh token expiration time in seconds
                ],
                'user' => $user,
            ];

            DB::commit();
            return self::success($data, __('messages.registered_successfully'), 201); // Use HTTP 201 for resource creation
        } catch (\Exception $e) {
            // Log the error and return a generic response
            Log::error('User Registration Error:', ['error' => $e]);

            DB::rollback();
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        // Validate the incoming request
        $validateRequest = Validator::make($request->all(), [
            'userName' => 'required',
            'password' => 'required|string',
        ]);
        if($validateRequest->fails()){
            return self::error(__('messages.validation_error'), 422, $validateRequest->getMessageBag()->toArray());
        }

        $credentials = $request->only('userName', 'password');

        // Attempt to authenticate and get a token
        if(!$token = auth()->attempt(array_merge($credentials, ['active' => 1]))) {
            return self::error(__('messages.invalid_credentials'), 401);
        }

        // Get authenticated user and filter required attributes
        // If authentication passed, get the user
        $user = auth()->user();

        // Update last_ip, login_time, last_activity
        $user->last_ip = $request->ip();
        $user->login_time = now();
        $user->last_activity = now();
        $user->logged = 1;
        $user->save();

        $userData = $user->only(['id', 'userName', 'name', 'Department', 'branch', 'login_time']);

        // Get the first role of the user (assuming a user has at least one role)
        $role = $user->getRoleNames()->first(); // Returns a single role name

        $redirectUrl = '/admin/dashboard';

        // Respond with the token
        return $this->respondWithToken($token, null, array_merge($userData, ['redirect_url' => $redirectUrl, 'role' => $role]));
    }

    public function refresh(): \Illuminate\Http\JsonResponse
    {
        $newToken = JWTAuth::parseToken()->refresh();
        return $this->respondWithToken($newToken);
    }

    public function respondWithToken($token, $refresh_token = null, $user=null): \Illuminate\Http\JsonResponse
    {
        $data = [
            'token' => [
                'accessToken' => $token,
//                'refreshToken' => auth()->setTTL(config('jwt.refresh_ttl'))->refresh(),
                'type' => 'bearer',
                'expires_in' => (int) config('jwt.ttl', 60) * 60, // Access token expiration in seconds
                'refresh_expires_in' => (int) config('jwt.refresh_ttl', 20160) * 60, // Refresh token expiration time in seconds
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

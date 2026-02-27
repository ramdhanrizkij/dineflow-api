<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $accessToken  = Auth::login($user);
        $refreshToken = $this->generateRefreshToken($user);

        return ApiResponse::created([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type'    => 'bearer',
            'expires_in'    => config('jwt.ttl') * 60,
            'user'          => $user,
        ], 'User registered successfully');
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::attempt($credentials)) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        $user         = Auth::user();
        $refreshToken = $this->generateRefreshToken($user);

        return ApiResponse::success([
            'access_token'  => $token,
            'refresh_token' => $refreshToken,
            'token_type'    => 'bearer',
            'expires_in'    => config('jwt.ttl') * 60,
            'user'          => $user,
        ], 'Login successful');
    }

    public function profile(): JsonResponse
    {
        return ApiResponse::success(Auth::user(), 'User profile retrieved successfully');
    }

    public function logout(): JsonResponse
    {
        Auth::logout();

        return ApiResponse::success(null, 'Successfully logged out');
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $refreshToken = $request->bearerToken();

            if (!$refreshToken) {
                return ApiResponse::error('Refresh token not provided', 401);
            }

            $payload = JWTAuth::setToken($refreshToken)->getPayload();

            if ($payload->get('type') !== 'refresh') {
                return ApiResponse::error('Invalid token type', 401);
            }

            $user = JWTAuth::setToken($refreshToken)->authenticate();

            if (!$user) {
                return ApiResponse::error('User not found', 401);
            }

            $newAccessToken  = Auth::login($user);
            $newRefreshToken = $this->generateRefreshToken($user);

            return ApiResponse::success([
                'access_token'  => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                'token_type'    => 'bearer',
                'expires_in'    => config('jwt.ttl') * 60,
            ], 'Token refreshed successfully');

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ApiResponse::error('Refresh token expired', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error('Invalid refresh token', 401);
        } catch (\Exception $e) {
            return ApiResponse::error('Could not refresh token', 500);
        }
    }

    protected function generateRefreshToken($user): string
    {
        $customClaims = [
            'type' => 'refresh',
            'exp'  => now()->addMinutes(config('jwt.refresh_ttl'))->timestamp,
        ];

        return JWTAuth::customClaims($customClaims)->fromUser($user);
    }
}
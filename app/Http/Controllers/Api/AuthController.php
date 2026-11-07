<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** POST /api/register */
    public function register(RegisterRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated() + ['role' => 'customer']);
        $token = $customer->createToken('api-token')->plainTextToken;

        return ApiResponse::success([
            'customer' => new CustomerResource($customer),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Registrasi berhasil.', 201);
    }

    /** POST /api/login */
    public function login(LoginRequest $request): JsonResponse
    {
        $customer = Customer::where('email', $request->validated('email'))->first();

        if (! $customer || ! Hash::check($request->validated('password'), $customer->password)) {
            return ApiResponse::error('Email atau password salah.', 401);
        }

        $token = $customer->createToken('api-token')->plainTextToken;

        return ApiResponse::success([
            'customer' => new CustomerResource($customer),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login berhasil.');
    }

    /** GET /api/me */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new CustomerResource($request->user()),
            'Data profil berhasil diambil.'
        );
    }

    /** PUT /api/profile */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $customer = $request->user();
        $customer->update($request->validated());

        return ApiResponse::success(
            new CustomerResource($customer->fresh()),
            'Profil berhasil diperbarui.'
        );
    }

    /** POST /api/logout */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logout berhasil. Token sudah tidak berlaku.');
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Http\Requests\RefreshTokenRequest;
use App\Exceptions\InvalidTokenException;
class AuthController extends Controller
{

    public function __construct(private AuthService $authService)
      {}  

    /**
     * API Login, on success return JWT Auth token
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $loginResponse = $this->authService->login($request->validated());
        
        return response()->json(['data' => new LoginResource($loginResponse)], 200);
            
    }


    /**
     * Refresh access token using refresh token
     *
     * @param RefreshTokenRequest $request
     * @return JsonResponse
     */
    public function refreshUserTokens(RefreshTokenRequest $request): JsonResponse
    {   
        $newTokens = $this->authService->refreshUserTokens($request->validated()['refreshToken']);

        return response()->json([
            'data' => $newTokens

        ], 200);
    }

    /**
     * Logout user (revoke current access token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        
        if ($token) {
            $this->authService->logout($request->user()->id);
        }
        
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

}

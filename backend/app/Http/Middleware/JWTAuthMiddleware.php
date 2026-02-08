<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JWTService;
use App\Exceptions\UnauthenticatedException;
use App\Models\User;
use App\Repositories\DB\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class JWTAuthMiddleware
{
    public function __construct(
        private JWTService $jwtService,
        private UserRepository $userRepository,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
public function handle(Request $request, Closure $next): Response
{
    $token = $this->extractToken($request); 
    $user = $this->authenticate($token);    
    
    // $request->setUserResolver(fn() => $user);
    auth()->setUser($user);
    
    return $next($request);
}

private function extractToken(Request $request): string
{
    $token = $request->bearerToken();
    
    if (!$token) {
        throw new UnauthenticatedException('Token not provided');
    }
    
    return $token;
}

private function authenticate(string $token): User
{
    $userId = $this->jwtService->validateToken($token,'access');    
    $user = $this->userRepository->findById($userId);
    
    if (!$user) {
        throw new UnauthenticatedException('User not found');
    }
    
    return $user;
}

}
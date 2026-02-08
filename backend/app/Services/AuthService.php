<?php
namespace App\Services;
use App\Models\User;
use App\Repositories\DB\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\InvalidCredentialsException;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private JWTService $jwtService
    )
    {
    }
    public function login(array $credentials): array
    {
        $user = $this->getAuthenticatedUser($credentials['email'],$credentials['password']);
        
        $tokens = $this->jwtService->generateTokens($user->id);
        
        return ['user' => $user,'tokens' => $tokens,];
    }

    public function getAuthenticatedUser(string $email, string $password)
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !Hash::check($password, $user->password)) {  
            throw new InvalidCredentialsException();  
        }

        return $user;
    }


    
      public function refreshUserTokens(string $refreshToken): array
    {
        return $this->jwtService->refreshUserTokens($refreshToken);
    }

    public function logout(string $userId): bool
    {
        return $this->jwtService->revokeUserTokens($userId);
    }
    

}
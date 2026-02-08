<?php

namespace App\Services;


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Exceptions\InvalidTokenException;
use App\Repositories\Contracts\AuthRepositoryContract;
use Exception;

/**
 * JWT Service
 * Handles JWT token generation, validation, and Redis caching
 */
class JWTService
{
    
    private string $secretKey;
    private string $algorithm;
    private int $accessTokenTTL;
    private int $refreshTokenTTL; 
    
    public function __construct(private AuthRepositoryContract $authRepo)
    {
        $this->secretKey = config('jwt.secret');
        $this->algorithm = config('jwt.algorithm', 'HS256');
        $this->accessTokenTTL = config('jwt.access_token_ttl', 15);
        $this->refreshTokenTTL = config('jwt.refresh_token_ttl', 10080); 
    }
    
     /**
     * Generate access and refresh tokens for a user
     * 
     * @param string $userId
     * @return array
     */
    public function generateTokens(string $userId): array
    {

        $accessToken = $this->createAccessToken($userId);
        $refreshToken = $this->createRefreshToken($userId);
        
        $this->authRepo->setTokens($userId, $accessToken, $refreshToken);
        
        return ['accessToken' => $accessToken,'refreshToken' => $refreshToken,];
    }


    /**
     * Create access token
     * 
     * CHANGED: Takes userId string instead of User object
     * 
     * @param string $userId
     * @return string
     */
    private function createAccessToken(string $userId): string
    {
        $payload = [
            'iss' => config('app.url'), // Issuer
            'sub' => $userId, // Subject (user ID)
            'iat' => time(), // Issued at
            'exp' => time() + ($this->accessTokenTTL * 60), // Expiration
            'type' => 'access',
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }
    
    /**
     * Create refresh token
     * 
     * CHANGED: Takes userId string instead of User object
     * 
     * @param string $userId
     * @return string
     */
    private function createRefreshToken(string $userId): string
    {
        $payload = [
            'iss' => config('app.url'),
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + ($this->refreshTokenTTL * 60),
            'type' => 'refresh',
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }
    
    
    
    /**
     * Check if token is active in Redis
     * 
     * @param string $token
     * @param string $userId
     * @param string $type Token type ('access' or 'refresh')
     * @return bool
     */
    public function isActiveToken(string $token, string $userId, string $type): bool
    {
        // Get stored token from Redis based on type
        $storedToken = ($type === 'refresh')
            ? $this->authRepo->getRefreshToken($userId)
            : $this->authRepo->getAccessToken($userId);
        
        if($storedToken === null){
            if(!$this->authRepo->isStorageAvailable()){
                return true;  // Storage down, allow token
            }

            return false;  // Storage up but token not found
        }
        return  $storedToken === $token;
    }

    /**
     * Refresh access token using refresh token
     * 
     * @param string $refreshToken
     * @return array|null New tokens if successful
     */
    public function refreshUserTokens(string $refreshToken): array
    {
        $userId = $this->validateToken($refreshToken,'refresh');

        return $this->generateTokens($userId);
    }
    
    /**
     * Revoke user tokens
     * 
     * @param string $token
     * @return bool
     */
    public function revokeUserTokens(string $userId): bool
    {
        return $this->authRepo->deleteUserTokens($userId);
    }


    public function validateToken(string $token, string $type): string
    {
        $userId = $this->decodeToken($token, $type);
        
        if (!$userId || !$this->isActiveToken($token,$userId,$type)) {
            throw new InvalidTokenException();  
        }
        return $userId;
    }

    /**
     * Decode token payload (without validation)
     * 
     * @param string $token
     *@return string|null
     **/

    public function decodeToken(string $token,string $type): ?string
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            if (!isset($decoded->type) || $decoded->type !== $type) {
                throw new InvalidTokenException(); 
            }
            return isset($decoded->sub) ? (string) $decoded->sub : null;
        } catch (Exception $e) {
            throw new InvalidTokenException(); 
        }
    }

}
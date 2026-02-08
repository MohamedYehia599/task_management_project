<?php

namespace App\Repositories\Redis;
use Illuminate\Support\Facades\Redis;
use App\Services\RedisClient;
use App\Repositories\Contracts\AuthRepositoryContract;

/**
 * Redis Authentication Repository with Circuit Breaker
 * 
 * Handles token storage with graceful degradation when Redis is unavailable
 */
class RedisAuthRepo implements AuthRepositoryContract
{
    private int $accessTokenTTL;
    private int $refreshTokenTTL;
    
    public function __construct(private RedisClient $redisClient)
    {
        $this->accessTokenTTL = config('jwt.access_token_ttl', 15) * 60;
        $this->refreshTokenTTL = config('jwt.refresh_token_ttl', 10080) * 60;
    }
    
    private function getAccessTokenKey(string $userId): string
    {
        $prefix = config('jwt.redis.access_token_prefix', 'activeAccessToken:');
        return $prefix . $userId;
    }
    
    private function getRefreshTokenKey(string $userId): string
    {
        $prefix = config('jwt.redis.refresh_token_prefix', 'activeRefreshToken:');
        return $prefix . $userId;
    }
    
    /**
     *Check if storage is available
     */
    public function isStorageAvailable(): bool
    {
        return $this->redisClient->isHealthy();
    }
    
    /**
     * Set user's tokens
     */
    public function setTokens(string $userId, string $accessToken, string $refreshToken): bool
    {
        $this->setAccessToken($userId, $accessToken);
        $this->setRefreshToken($userId, $refreshToken);
        return true;
    }
    
    public function setAccessToken(string $userId, string $accessToken): bool
    {
        return $this->redisClient->setex(
            $this->getAccessTokenKey($userId), 
            $this->accessTokenTTL, 
            $accessToken
        );
    }
    
    public function setRefreshToken(string $userId, string $refreshToken): bool
    {
        return $this->redisClient->setex(
            $this->getRefreshTokenKey($userId), 
            $this->refreshTokenTTL, 
            $refreshToken
        );
    }
    
    public function getAccessToken(string $userId): ?string
    {
        return $this->redisClient->get($this->getAccessTokenKey($userId));
    }
    
    public function getRefreshToken(string $userId): ?string
    {
        return $this->redisClient->get($this->getRefreshTokenKey($userId));
    }
    
    public function deleteUserTokens(string $userId): bool
    {
        return $this->redisClient->del([
            $this->getAccessTokenKey($userId),
            $this->getRefreshTokenKey($userId)
        ]);
    }

}
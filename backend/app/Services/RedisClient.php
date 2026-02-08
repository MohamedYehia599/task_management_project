<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

/**
 * Resilient Redis Client
 * 
 * Reusable service for all Redis operations with built-in circuit breaker
 * Can be injected into any repository that needs Redis
 */
class RedisClient
{
    public function __construct(
        private RedisCircuitBreaker $circuitBreaker
    ) {}
    
    /**
     * Execute Redis command with circuit breaker
     */
    public function execute(callable $operation, $default = null)
    {
        return $this->circuitBreaker->execute($operation, $default);
    }
    
    /**
     * Get value from Redis
     */
    public function get(string $key, $default = null): mixed
    {
        return $this->circuitBreaker->execute(
            fn() => Redis::get($key),
            default: $default
        );
    }
    
    /**
     * Set value in Redis with TTL
     */
    public function setex(string $key, int $ttl, string $value): bool
    {
        $result = $this->circuitBreaker->execute(
            fn() => Redis::setex($key, $ttl, $value),
            default: false
        );
        
        return $result !== false;
    }
    
    /**
     * Delete keys from Redis
     */
    public function del(array $keys): bool
    {
        $result = $this->circuitBreaker->execute(
            fn() => Redis::del($keys),
            default: 0
        );
        
        return $result > 0;
    }
    
    /**
     * Check if Redis is healthy
     */
    public function isHealthy(): bool
    {
        return $this->circuitBreaker->isHealthy();
    }
}
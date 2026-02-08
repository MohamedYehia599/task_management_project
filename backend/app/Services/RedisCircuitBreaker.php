<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Simple Circuit Breaker - Just Cached Health Checks
 * 
 * Simplest possible approach:
 * - Ping Redis every 5 seconds (cached)
 * - If unhealthy, skip Redis operations
 */
class RedisCircuitBreaker
{
    private const HEALTH_CHECK_TTL = 5; // Cache health for 5 seconds
    private const HEALTH_KEY = 'redis:healthy';
    
    /**
     * Execute Redis operation with health check
     */
    public function execute(callable $operation, $default = null)
    {

        // Check if Redis is healthy (cached check, fast)
        if (!$this->isHealthy()) {
            return $default;
        }
        
        try {
            return $operation();
        } catch (\Exception $e) {
            Log::error('Redis operation failed: ' . $e->getMessage());
            
            // Mark as unhealthy immediately
            Cache::put(self::HEALTH_KEY, false, self::HEALTH_CHECK_TTL);
            
            return $default;
        }
    }
    
    /**
     * Check Redis health (with 5-second caching)
     */
    public function isHealthy(): bool
    {
        $cachedHealth = Cache::get(self::HEALTH_KEY);
        
        if ($cachedHealth !== null) {
            return $cachedHealth; // Use cached result (no ping needed)
        }
        
        // No cached result - do actual health check
        try {
            Redis::ping();
            $isHealthy = true;
        } catch (\Exception $e) {
            $isHealthy = false;
            Log::warning('Redis health check failed');
        }
        
        Cache::put(self::HEALTH_KEY, $isHealthy, self::HEALTH_CHECK_TTL);
        
        return $isHealthy;
    }
}

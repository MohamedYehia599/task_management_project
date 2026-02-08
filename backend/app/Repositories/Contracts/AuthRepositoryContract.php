<?php

namespace App\Repositories\Contracts;

/**
 * Authentication Repository Contract
 * 
 * Defines interface for authentication token storage operations
 * Can be implemented by Redis, Database, or any other storage mechanism
 */
interface AuthRepositoryContract
{
    /**
     * Store both access and refresh tokens for a user
     * 
     * @param string $userId
     * @param string $accessToken
     * @param string $refreshToken
     * @return bool Success status
     */
    public function setTokens(string $userId, string $accessToken, string $refreshToken): bool;

    /**
     * Get access token for a user
     * 
     * @param string $userId
     * @return string|null Token or null if not found
     */
    public function getAccessToken(string $userId): ?string;

    /**
     * Get refresh token for a user
     * 
     * @param string $userId
     * @return string|null Token or null if not found
     */
    public function getRefreshToken(string $userId): ?string;

    /**
     * Delete all tokens for a user (logout)
     * 
     * @param string $userId
     * @return bool Success status
     */
    public function deleteUserTokens(string $userId): bool;

    /**
     * Check if the storage mechanism is available
     * 
     * @return bool True if available, false otherwise
     */
    public function isStorageAvailable(): bool;
}
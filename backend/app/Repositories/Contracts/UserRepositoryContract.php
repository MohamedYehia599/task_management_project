<?php

namespace App\Repositories\Contracts;

use App\Models\User;

/**
 * User Repository Contract
 * 
 * Defines interface for user data access operations
 */
interface UserRepositoryContract
{
    /**
     * Find user by email address
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find user by ID
     * 
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;
}
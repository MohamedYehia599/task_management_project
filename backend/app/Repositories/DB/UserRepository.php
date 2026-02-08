<?php
namespace App\Repositories\DB;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;


class UserRepository implements UserRepositoryContract
{
    /**
     * Find user by email address
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', strtolower(trim($email)))->first();
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }
}
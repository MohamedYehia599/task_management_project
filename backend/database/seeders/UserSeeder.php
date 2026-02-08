<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\UserRoles;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if test admin already exists (idempotent check)
        if (!User::where('email', 'manager1@test.com')->exists()) {
            // Create 2 specific test admins
            User::factory()->create([
                'name' => 'Manager One',
                'email' => 'manager1@test.com',
                'role' => UserRoles::MANAGER,
                'password' => Hash::make('manager123'),
            ]);

            User::factory()->create([
                'name' => 'Manager Two', 
                'email' => 'manager2@test.com',
                'role' => UserRoles::MANAGER,
                'password' => Hash::make('manager123'),
            ]);

            // Create 2 specific test users
            User::factory()->create([
                'name' => 'User One',
                'email' => 'user1@test.com',
                'role' => UserRoles::USER,
                'password' => Hash::make('user123'),
            ]);

            User::factory()->create([
                'name' => 'User Two',
                'email' => 'user2@test.com',
                'role' => UserRoles::USER,
                'password' => Hash::make('user123'),
            ]);

            echo "Created 4 specific test users (2 managers, 2 users)\n";
        } else {
            echo "Test users already exist, skipping...\n";
        }

        // Create additional random users (98 more to reach 100 total)
        $existingCount = User::count();
        $remaining = 100 - $existingCount;
        
        if ($remaining > 0) {
            echo "Creating {$remaining} additional random users...\n";
            
            // Create 48 more random users (ensuring we have at least 50 users total)
            $randomUsersToCreate = max(48, $remaining);
            
            // Create mix of admins and regular users
            $adminCount = (int) ($randomUsersToCreate * 0.2); // 20% admins
            
            User::factory()->count($adminCount)->admin()->create();
            User::factory()->count($randomUsersToCreate - $adminCount)->regular()->create();
            
            echo "Created {$randomUsersToCreate} additional random users\n";
        }
    }
}
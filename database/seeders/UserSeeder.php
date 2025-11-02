<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the admin role exists (Spatie permission)
        $role = Role::firstOrCreate(['name' => 'admin']);

        // Create or update the admin user
        $user = User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'user_type' => 'super_admin',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        // Assign role if available
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($role);
        }
    }
}

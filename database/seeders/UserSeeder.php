<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@b2b.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Customer User
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@b2b.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: admin@b2b.com / password');
        $this->command->info('Customer: customer@b2b.com / password');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $users = [
            [
                'id' => 'b8caf6c4-4b8d-4447-8ffc-f335f2eba7ae',
                'first_name' => 'WrapCrop',
                'last_name' => 'admin',
                'gender' => 'male',
                'email' => 'wrapcrop@gmail.com',
                'role' => 'admin',
                'password' => 'Password123!',
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Ian',
                'last_name' => 'Calica',
                'gender' => 'male',
                'email' => 'iggc654@gmail.com',
                'role' => 'user',
                'password' => 'Password123!',
            ],
            [
                'id' => '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
                'first_name' => 'Kyle',
                'last_name' => 'Candelario',
                'gender' => 'male',
                'email' => 'kyle.candelario@gmail.com',
                'role' => 'user',
                'password' => 'Password123!',
            ],
            [
                'id' => '7f9c0c2d-3e4f-5a6b-7c8d-9e0f1a2b3c4d',
                'first_name' => 'Anna',
                'last_name' => 'Barcelona',
                'gender' => 'female',
                'email' => 'anna.barcelona@gmail.com',
                'role' => 'user',
                'password' => 'Password123!',
            ],
            [
                'id' => '8a9b0c1d-2e3f-4a5b-6c7d-8e9f0a1b2c3d',
                'first_name' => 'Gelain',
                'last_name' => 'Castronuevo',
                'gender' => 'male',
                'email' => 'gelain.castronuevo@gmail.com',
                'role' => 'user',
                'password' => 'Password123!',
            ],
        ];

        foreach ($users as $user) {
            // Hash the password
            $user['password'] = Hash::make($user['password']);
            User::create($user);
        }

        // Display created accounts
        $this->command->info('=' . str_repeat('=', 50));
        $this->command->info('Created 5 user accounts:');
        $this->command->info('=' . str_repeat('=', 50));
        
        foreach ($users as $index => $user) {
            $this->command->line(($index + 1) . ". {$user['first_name']} {$user['last_name']} - {$user['role']} ({$user['email']})");
        }
        
        $this->command->info('=' . str_repeat('=', 50));
        $this->command->info('Default passwords are set as provided above.');
    }
}
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\TreeSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\FlowerSeeder;
use Database\Seeders\FruitSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TreeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(FlowerSeeder::class);
        $this->call(FruitSeeder::class);
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

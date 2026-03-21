<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
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
            // Clear tables first
        DB::table('fruits')->truncate();
        DB::table('flowers')->truncate();
        DB::table('trees')->truncate();
        DB::table('users')->truncate();


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

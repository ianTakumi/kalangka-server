<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tree;
use App\Models\Flower;
use Carbon\Carbon;

class FlowerSeeder extends Seeder
{
    public function run(): void
    {
        Flower::truncate();

        $trees = Tree::all();
        
        if ($trees->isEmpty()) {
            $this->command->error('⚠️  No trees found. Please run TreeSeeder first.');
            return;
        }

        $this->command->info('====================================');
        $this->command->info('🌸 Creating flowers for ' . $trees->count() . ' trees...');
        $this->command->info('====================================');

        $imageUrl = 'https://gujmgaqntmdvqvvlwqhf.supabase.co/storage/v1/object/public/kalangka/Flower/langka-flower.jpg';
        
        $totalFlowers = 0;

        // HARDCODED REAL UUIDs for flowers (2 per tree = 40 flowers total)
        $flowerIds = [
            // For East Langka #1 (id: 11111111-1111-1111-1111-111111111111)
            '1d92e857-045d-44f4-935c-61a5f0637881',
            '2a3f8b9c-4d5e-6f7a-8b9c-0d1e2f3a4b5c',
            
            // For East Langka #2 (id: 22222222-2222-2222-2222-222222222222)
            '3b4c5d6e-7f8a-9b0c-1d2e-3f4a5b6c7d8e',
            '4c5d6e7f-8a9b-0c1d-2e3f-4a5b6c7d8e9f',
            
            // For East Langka #3 (id: 33333333-3333-3333-3333-333333333333)
            '5d6e7f8a-9b0c-1d2e-3f4a-5b6c7d8e9f0a',
            '6e7f8a9b-0c1d-2e3f-4a5b-6c7d8e9f0a1b',
            
            // For East Langka #4 (id: 44444444-4444-4444-4444-444444444444)
            '7f8a9b0c-1d2e-3f4a-5b6c-7d8e9f0a1b2c',
            '8a9b0c1d-2e3f-4a5b-6c7d-8e9f0a1b2c3d',
            
            // For East Langka #5 (id: 55555555-5555-5555-5555-555555555555)
            '9b0c1d2e-3f4a-5b6c-7d8e-9f0a1b2c3d4e',
            '0c1d2e3f-4a5b-6c7d-8e9f-0a1b2c3d4e5f',
            
            // For North Langka #1 (id: 66666666-6666-6666-6666-666666666666)
            '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d',
            '2b3c4d5e-6f7a-8b9c-0d1e-2f3a4b5c6d7e',
            
            // For North Langka #2 (id: 77777777-7777-7777-7777-777777777777)
            '3c4d5e6f-7a8b-9c0d-1e2f-3a4b5c6d7e8f',
            '4d5e6f7a-8b9c-0d1e-2f3a-4b5c6d7e8f9a',
            
            // For North Langka #3 (id: 88888888-8888-8888-8888-888888888888)
            '5e6f7a8b-9c0d-1e2f-3a4b-5c6d7e8f9a0b',
            '6f7a8b9c-0d1e-2f3a-4b5c-6d7e8f9a0b1c',
            
            // For North Langka #4 (id: 99999999-9999-9999-9999-999999999999)
            '7a8b9c0d-1e2f-3a4b-5c6d-7e8f9a0b1c2d',
            '8b9c0d1e-2f3a-4b5c-6d7e-8f9a0b1c2d3e',
            
            // For North Langka #5 (id: aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa)
            '9c0d1e2f-3a4b-5c6d-7e8f-9a0b1c2d3e4f',
            '0d1e2f3a-4b5c-6d7e-8f9a-0b1c2d3e4f5a',
            
            // For South Langka #1 (id: bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb)
            '1e2f3a4b-5c6d-7e8f-9a0b-1c2d3e4f5a6b',
            '2f3a4b5c-6d7e-8f9a-0b1c-2d3e4f5a6b7c',
            
            // For South Langka #2 (id: cccccccc-cccc-cccc-cccc-cccccccccccc)
            '3a4b5c6d-7e8f-9a0b-1c2d-3e4f5a6b7c8d',
            '4b5c6d7e-8f9a-0b1c-2d3e-4f5a6b7c8d9e',
            
            // For South Langka #3 (id: dddddddd-dddd-dddd-dddd-dddddddddddd)
            '5c6d7e8f-9a0b-1c2d-3e4f-5a6b7c8d9e0f',
            '6d7e8f9a-0b1c-2d3e-4f5a-6b7c8d9e0f1a',
            
            // For South Langka #4 (id: eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee)
            '7e8f9a0b-1c2d-3e4f-5a6b-7c8d9e0f1a2b',
            '8f9a0b1c-2d3e-4f5a-6b7c-8d9e0f1a2b3c',
            
            // For South Langka #5 (id: ffffffff-ffff-ffff-ffff-ffffffffffff)
            '9a0b1c2d-3e4f-5a6b-7c8d-9e0f1a2b3c4d',
            '0b1c2d3e-4f5a-6b7c-8d9e-0f1a2b3c4d5e',
            
            // For West Langka #1 (id: 11111111-2222-3333-4444-555555555555)
            '1c2d3e4f-5a6b-7c8d-9e0f-1a2b3c4d5e6f',
            '2d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a',
            
            // For West Langka #2 (id: 22222222-3333-4444-5555-666666666666)
            '3e4f5a6b-7c8d-9e0f-1a2b-3c4d5e6f7a8b',
            '4f5a6b7c-8d9e-0f1a-2b3c-4d5e6f7a8b9c',
            
            // For West Langka #3 (id: 33333333-4444-5555-6666-777777777777)
            '5a6b7c8d-9e0f-1a2b-3c4d-5e6f7a8b9c0d',
            '6b7c8d9e-0f1a-2b3c-4d5e-6f7a8b9c0d1e',
            
            // For West Langka #4 (id: 44444444-5555-6666-7777-888888888888)
            '7c8d9e0f-1a2b-3c4d-5e6f-7a8b9c0d1e2f',
            '8d9e0f1a-2b3c-4d5e-6f7a-8b9c0d1e2f3a',
            
            // For West Langka #5 (id: 55555555-6666-7777-8888-999999999999)
            '9e0f1a2b-3c4d-5e6f-7a8b-9c0d1e2f3a4b',
            '0f1a2b3c-4d5e-6f7a-8b9c-0d1e2f3a4b5c',
        ];

        $flowerIndex = 0;

        foreach ($trees as $tree) {
            $this->command->info("📦 Processing tree: {$tree->description}");
            
            // Create 2 flowers per tree
            for ($i = 1; $i <= 2; $i++) {
                // Random date within the last 30 days
                $randomDate = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));
                
                // 70% chance of being wrapped
                $isWrapped = rand(1, 100) <= 70;
                
                Flower::create([
                    'id' => $flowerIds[$flowerIndex++],
                    'tree_id' => $tree->id,
                    'quantity' => rand(1, 5),
                    'wrapped_at' => $isWrapped ? $randomDate : $randomDate,
                    'image_url' => $imageUrl,
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);
                
                $totalFlowers++;
            }
            
            $this->command->info("   ✅ Created 2 flowers for {$tree->description}");
        }

        $this->command->info('====================================');
        $this->command->info('🌸 TOTAL FLOWERS CREATED: ' . $totalFlowers);
        $this->command->info('====================================');
        
        // Show first few IDs as sample
        $this->command->info('📋 Sample Flower IDs:');
        $this->command->info('   ' . $flowerIds[0]);
        $this->command->info('   ' . $flowerIds[1]);
        $this->command->info('   ' . $flowerIds[2]);
        $this->command->info('====================================');
    }
}
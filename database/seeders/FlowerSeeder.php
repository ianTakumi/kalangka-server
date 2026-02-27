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

        // HARDCODED UUIDs for flowers (2 per tree)
        $flowerIds = [
            // For East Langka #1 (id: 11111111-1111-1111-1111-111111111111)
            '11111111-1111-1111-1111-222222222222',
            '11111111-1111-1111-1111-333333333333',
            
            // For East Langka #2 (id: 22222222-2222-2222-2222-222222222222)
            '22222222-2222-2222-2222-333333333333',
            '22222222-2222-2222-2222-444444444444',
            
            // For East Langka #3 (id: 33333333-3333-3333-3333-333333333333)
            '33333333-3333-3333-3333-444444444444',
            '33333333-3333-3333-3333-555555555555',
            
            // For East Langka #4 (id: 44444444-4444-4444-4444-444444444444)
            '44444444-4444-4444-4444-555555555555',
            '44444444-4444-4444-4444-666666666666',
            
            // For East Langka #5 (id: 55555555-5555-5555-5555-555555555555)
            '55555555-5555-5555-5555-666666666666',
            '55555555-5555-5555-5555-777777777777',
            
            // For North Langka #1 (id: 66666666-6666-6666-6666-666666666666)
            '66666666-6666-6666-6666-777777777777',
            '66666666-6666-6666-6666-888888888888',
            
            // For North Langka #2 (id: 77777777-7777-7777-7777-777777777777)
            '77777777-7777-7777-7777-888888888888',
            '77777777-7777-7777-7777-999999999999',
            
            // For North Langka #3 (id: 88888888-8888-8888-8888-888888888888)
            '88888888-8888-8888-8888-999999999999',
            '88888888-8888-8888-8888-aaaaaaaaaaaa',
            
            // For North Langka #4 (id: 99999999-9999-9999-9999-999999999999)
            '99999999-9999-9999-9999-aaaaaaaaaaaa',
            '99999999-9999-9999-9999-bbbbbbbbbbbb',
            
            // For North Langka #5 (id: aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa)
            'aaaaaaaa-aaaa-aaaa-aaaa-bbbbbbbbbbbb',
            'aaaaaaaa-aaaa-aaaa-aaaa-cccccccccccc',
            
            // For South Langka #1 (id: bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb)
            'bbbbbbbb-bbbb-bbbb-bbbb-cccccccccccc',
            'bbbbbbbb-bbbb-bbbb-bbbb-dddddddddddd',
            
            // For South Langka #2 (id: cccccccc-cccc-cccc-cccc-cccccccccccc)
            'cccccccc-cccc-cccc-cccc-dddddddddddd',
            'cccccccc-cccc-cccc-cccc-eeeeeeeeeeee',
            
            // For South Langka #3 (id: dddddddd-dddd-dddd-dddd-dddddddddddd)
            'dddddddd-dddd-dddd-dddd-eeeeeeeeeeee',
            'dddddddd-dddd-dddd-dddd-ffffffffffff',
            
            // For South Langka #4 (id: eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee)
            'eeeeeeee-eeee-eeee-eeee-ffffffffffff',
            'eeeeeeee-eeee-eeee-eeee-111111111111',
            
            // For South Langka #5 (id: ffffffff-ffff-ffff-ffff-ffffffffffff)
            'ffffffff-ffff-ffff-ffff-111111111111',
            'ffffffff-ffff-ffff-ffff-222222222222',
            
            // For West Langka #1 (id: 11111111-2222-3333-4444-555555555555)
            '11111111-2222-3333-4444-666666666666',
            '11111111-2222-3333-4444-777777777777',
            
            // For West Langka #2 (id: 22222222-3333-4444-5555-666666666666)
            '22222222-3333-4444-5555-777777777777',
            '22222222-3333-4444-5555-888888888888',
            
            // For West Langka #3 (id: 33333333-4444-5555-6666-777777777777)
            '33333333-4444-5555-6666-888888888888',
            '33333333-4444-5555-6666-999999999999',
            
            // For West Langka #4 (id: 44444444-5555-6666-7777-888888888888)
            '44444444-5555-6666-7777-999999999999',
            '44444444-5555-6666-7777-aaaaaaaaaaaa',
            
            // For West Langka #5 (id: 55555555-6666-7777-8888-999999999999)
            '55555555-6666-7777-8888-aaaaaaaaaaaa',
            '55555555-6666-7777-8888-bbbbbbbbbbbb',
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
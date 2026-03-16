<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flower;
use App\Models\Tree;
use App\Models\Fruit;
use Carbon\Carbon;

class FruitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all flowers
        $flowers = Flower::all();
        
        if ($flowers->isEmpty()) {
            $this->command->error('⚠️  No flowers found. Please run FlowerSeeder first.');
            return;
        }

        $this->command->info('====================================');
        $this->command->info('🍎 Creating fruits for ' . $flowers->count() . ' flowers...');
        $this->command->info('====================================');

        // Fruit image URL
        $imageUrl = 'https://gujmgaqntmdvqvvlwqhf.supabase.co/storage/v1/object/public/kalangka/fruits/samplefruit.jpg';
        
        $totalFruits = 0;
        $totalFlowerQuantity = 0;
        $totalFruitQuantity = 0;

        // Calculate base date (115 days ago from now)
        $baseDate = Carbon::now()->subDays(115);
        $this->command->info("📅 Base date (115 days ago): " . $baseDate->format('Y-m-d H:i:s'));
        $this->command->info('');

        // HARDCODED UUIDs for 80 fruits (40 flowers × 2 fruits)
        $fruitIds = [
            // ===== EAST LANGKA (Trees 1-5) =====
            // Tree 1 (East Langka #1) - Flower 1 & 2
            '11111111-1111-1111-2222-333333333333', // Fruit 1 for Flower 1
            '11111111-1111-1111-2222-444444444444', // Fruit 2 for Flower 1
            '11111111-1111-1111-3333-444444444444', // Fruit 1 for Flower 2
            '11111111-1111-1111-3333-555555555555', // Fruit 2 for Flower 2
            
            // Tree 2 (East Langka #2) - Flower 1 & 2
            '22222222-2222-2222-3333-444444444444',
            '22222222-2222-2222-3333-555555555555',
            '22222222-2222-2222-4444-555555555555',
            '22222222-2222-2222-4444-666666666666',
            
            // Tree 3 (East Langka #3) - Flower 1 & 2
            '33333333-3333-3333-4444-555555555555',
            '33333333-3333-3333-4444-666666666666',
            '33333333-3333-3333-5555-666666666666',
            '33333333-3333-3333-5555-777777777777',
            
            // Tree 4 (East Langka #4) - Flower 1 & 2
            '44444444-4444-4444-5555-666666666666',
            '44444444-4444-4444-5555-777777777777',
            '44444444-4444-4444-6666-777777777777',
            '44444444-4444-4444-6666-888888888888',
            
            // Tree 5 (East Langka #5) - Flower 1 & 2
            '55555555-5555-5555-6666-777777777777',
            '55555555-5555-5555-6666-888888888888',
            '55555555-5555-5555-7777-888888888888',
            '55555555-5555-5555-7777-999999999999',
            
            // ===== NORTH LANGKA (Trees 6-10) =====
            // Tree 6 (North Langka #1) - Flower 1 & 2
            '66666666-6666-6666-7777-888888888888',
            '66666666-6666-6666-7777-999999999999',
            '66666666-6666-6666-8888-999999999999',
            '66666666-6666-6666-8888-aaaaaaaaaaaa',
            
            // Tree 7 (North Langka #2) - Flower 1 & 2
            '77777777-7777-7777-8888-999999999999',
            '77777777-7777-7777-8888-aaaaaaaaaaaa',
            '77777777-7777-7777-9999-aaaaaaaaaaaa',
            '77777777-7777-7777-9999-bbbbbbbbbbbb',
            
            // Tree 8 (North Langka #3) - Flower 1 & 2
            '88888888-8888-8888-9999-aaaaaaaaaaaa',
            '88888888-8888-8888-9999-bbbbbbbbbbbb',
            '88888888-8888-8888-aaaa-bbbbbbbbbbbb',
            '88888888-8888-8888-aaaa-cccccccccccc',
            
            // Tree 9 (North Langka #4) - Flower 1 & 2
            '99999999-9999-9999-aaaa-bbbbbbbbbbbb',
            '99999999-9999-9999-aaaa-cccccccccccc',
            '99999999-9999-9999-bbbb-cccccccccccc',
            '99999999-9999-9999-bbbb-dddddddddddd',
            
            // Tree 10 (North Langka #5) - Flower 1 & 2
            'aaaaaaaa-aaaa-aaaa-bbbb-cccccccccccc',
            'aaaaaaaa-aaaa-aaaa-bbbb-dddddddddddd',
            'aaaaaaaa-aaaa-aaaa-cccc-dddddddddddd',
            'aaaaaaaa-aaaa-aaaa-cccc-eeeeeeeeeeee',
            
            // ===== SOUTH LANGKA (Trees 11-15) =====
            // Tree 11 (South Langka #1) - Flower 1 & 2
            'bbbbbbbb-bbbb-bbbb-cccc-dddddddddddd',
            'bbbbbbbb-bbbb-bbbb-cccc-eeeeeeeeeeee',
            'bbbbbbbb-bbbb-bbbb-dddd-eeeeeeeeeeee',
            'bbbbbbbb-bbbb-bbbb-dddd-ffffffffffff',
            
            // Tree 12 (South Langka #2) - Flower 1 & 2
            'cccccccc-cccc-cccc-dddd-eeeeeeeeeeee',
            'cccccccc-cccc-cccc-dddd-ffffffffffff',
            'cccccccc-cccc-cccc-eeee-ffffffffffff',
            'cccccccc-cccc-cccc-eeee-111111111111',
            
            // Tree 13 (South Langka #3) - Flower 1 & 2
            'dddddddd-dddd-dddd-eeee-ffffffffffff',
            'dddddddd-dddd-dddd-eeee-111111111111',
            'dddddddd-dddd-dddd-ffff-111111111111',
            'dddddddd-dddd-dddd-ffff-222222222222',
            
            // Tree 14 (South Langka #4) - Flower 1 & 2
            'eeeeeeee-eeee-eeee-ffff-111111111111',
            'eeeeeeee-eeee-eeee-ffff-222222222222',
            'eeeeeeee-eeee-eeee-1111-222222222222',
            'eeeeeeee-eeee-eeee-1111-333333333333',
            
            // Tree 15 (South Langka #5) - Flower 1 & 2
            'ffffffff-ffff-ffff-1111-222222222222',
            'ffffffff-ffff-ffff-1111-333333333333',
            'ffffffff-ffff-ffff-2222-333333333333',
            'ffffffff-ffff-ffff-2222-444444444444',
            
            // ===== WEST LANGKA (Trees 16-20) =====
            // Tree 16 (West Langka #1) - Flower 1 & 2
            '11111111-2222-3333-4444-555555555555',
            '11111111-2222-3333-4444-666666666666',
            '11111111-2222-3333-5555-666666666666',
            '11111111-2222-3333-5555-777777777777',
            
            // Tree 17 (West Langka #2) - Flower 1 & 2
            '22222222-3333-4444-5555-666666666666',
            '22222222-3333-4444-5555-777777777777',
            '22222222-3333-4444-6666-777777777777',
            '22222222-3333-4444-6666-888888888888',
            
            // Tree 18 (West Langka #3) - Flower 1 & 2
            '33333333-4444-5555-6666-777777777777',
            '33333333-4444-5555-6666-888888888888',
            '33333333-4444-5555-7777-888888888888',
            '33333333-4444-5555-7777-999999999999',
            
            // Tree 19 (West Langka #4) - Flower 1 & 2
            '44444444-5555-6666-7777-888888888888',
            '44444444-5555-6666-7777-999999999999',
            '44444444-5555-6666-8888-999999999999',
            '44444444-5555-6666-8888-aaaaaaaaaaaa',
            
            // Tree 20 (West Langka #5) - Flower 1 & 2
            '55555555-6666-7777-8888-999999999999',
            '55555555-6666-7777-8888-aaaaaaaaaaaa',
            '55555555-6666-7777-9999-aaaaaaaaaaaa',
            '55555555-6666-7777-9999-bbbbbbbbbbbb',
        ];

        $fruitIndex = 0;

        foreach ($flowers as $flower) {
            // Get the tree for display
            $tree = Tree::find($flower->tree_id);
            $treeDesc = $tree ? $tree->description : 'Unknown Tree';
            
            $this->command->info("📦 Processing flower from: {$treeDesc} (Flower quantity: {$flower->quantity})");
            
            // Track total fruit quantity for this flower
            $flowerFruitTotal = 0;
            $maxFruitPerFlower = $flower->quantity; // Can't exceed flower quantity
            
            // Create exactly 2 fruits per flower
            for ($i = 1; $i <= 2; $i++) {
                // Use base date (115 days ago) for all fruits
                $fruitCreatedAt = clone $baseDate;
                
                // All fruits are bagged (100% bagged rate)
                $isBagged = true;
                
                // Bagged date is the same as created_at (since they're all bagged)
                $baggedAt = clone $fruitCreatedAt;
                
                // Calculate fruit quantity based on remaining capacity
                $remainingCapacity = $maxFruitPerFlower - $flowerFruitTotal;
                
                if ($remainingCapacity <= 0) {
                    // No more capacity left for this flower
                    $fruitQuantity = 0;
                } elseif ($i == 2) {
                    // Last fruit for this flower - use all remaining capacity
                    $fruitQuantity = $remainingCapacity;
                } else {
                    // First fruit - take a portion of remaining capacity (30-70%)
                    $maxTake = min($remainingCapacity - 1, floor($remainingCapacity * 0.7));
                    $minTake = max(1, floor($remainingCapacity * 0.3));
                    
                    if ($maxTake < $minTake) {
                        $fruitQuantity = $remainingCapacity;
                    } else {
                        $fruitQuantity = rand($minTake, $maxTake);
                    }
                }
                
                if ($fruitQuantity > 0) {
                    Fruit::create([
                        'id' => $fruitIds[$fruitIndex++],
                        'flower_id' => $flower->id,
                        'tree_id' => $flower->tree_id,
                        'quantity' => $fruitQuantity,
                        'bagged_at' => $baggedAt,
                        'image_url' => $imageUrl,
                        'created_at' => $fruitCreatedAt,
                        'updated_at' => $fruitCreatedAt,
                    ]);
                    
                    $flowerFruitTotal += $fruitQuantity;
                    $totalFruits++;
                    $totalFruitQuantity += $fruitQuantity;
                    
                    $this->command->info("   🍎 Fruit {$i}: {$fruitQuantity} fruits (Running total: {$flowerFruitTotal}/{$maxFruitPerFlower})");
                    $this->command->info("      📅 Created: " . $fruitCreatedAt->format('Y-m-d H:i:s'));
                } else {
                    $this->command->info("   ⚠️  Fruit {$i}: No fruits (max capacity reached: {$flowerFruitTotal}/{$maxFruitPerFlower})");
                }
            }
            
            $totalFlowerQuantity += $flower->quantity;
            
            // Validate that we didn't exceed the flower's quantity
            if ($flowerFruitTotal > $flower->quantity) {
                $this->command->error("   ❌ ERROR: Total fruits ({$flowerFruitTotal}) exceeds flower quantity ({$flower->quantity})!");
            } else {
                $this->command->info("   ✅ Flower completed: {$flowerFruitTotal}/{$flower->quantity} fruits used");
            }
            
            $this->command->info('');
        }

        $this->command->info('====================================');
        $this->command->info('🍎 SUMMARY');
        $this->command->info('====================================');
        $this->command->info('🌸 Total flowers: ' . $flowers->count());
        $this->command->info('📊 Total flower quantity: ' . $totalFlowerQuantity);
        $this->command->info('🍎 Total fruit records created: ' . $totalFruits);
        $this->command->info('📊 Total fruit quantity: ' . $totalFruitQuantity);
        $this->command->info('📈 Utilization rate: ' . round(($totalFruitQuantity / $totalFlowerQuantity) * 100, 2) . '%');
        $this->command->info('📅 All fruits created on: ' . $baseDate->format('Y-m-d H:i:s'));
        $this->command->info('📅 All fruits bagged on: ' . $baseDate->format('Y-m-d H:i:s'));
        $this->command->info('📊 Days since bagged: 115 days');
        $this->command->info('====================================');
        $this->command->info('🖼️  Fruit Image: ' . $imageUrl);
        $this->command->info('====================================');
    }
}
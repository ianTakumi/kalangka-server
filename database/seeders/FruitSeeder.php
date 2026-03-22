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
        
        // Age categories distribution
        $ageCategories = [
            'approaching' => ['min' => 115, 'max' => 119, 'count' => 0, 'total' => 0],
            'ready' => ['min' => 120, 'max' => 124, 'count' => 0, 'total' => 0],
            'overdue' => ['min' => 125, 'max' => 130, 'count' => 0, 'total' => 0],
        ];

        $this->command->info('');
        $this->command->info('📅 Creating fruits with different ages:');
        $this->command->info('   🌱 Approaching: 115-119 days ago');
        $this->command->info('   ✅ Ready: 120-124 days ago');
        $this->command->info('   ⚠️  Overdue: 125-130 days ago');
        $this->command->info('');

        // HARDCODED UNIQUE REAL UUIDs for 80 fruits (40 flowers × 2 fruits)
        $fruitIds = [
            // ===== EAST LANGKA (Trees 1-5) =====
            // Tree 1 (East Langka #1) - Flower 1 & 2
            'a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d', // Fruit 1 for Flower 1
            'b2c3d4e5-f6a7-4b8c-9d0e-1f2a3b4c5d6e', // Fruit 2 for Flower 1
            'c3d4e5f6-a7b8-4c9d-0e1f-2a3b4c5d6e7f', // Fruit 1 for Flower 2
            'd4e5f6a7-b8c9-4d0e-1f2a-3b4c5d6e7f8a', // Fruit 2 for Flower 2
            
            // Tree 2 (East Langka #2) - Flower 1 & 2
            'e5f6a7b8-c9d0-4e1f-2a3b-4c5d6e7f8a9b',
            'f6a7b8c9-d0e1-4f2a-3b4c-5d6e7f8a9b0c',
            'a7b8c9d0-e1f2-4a3b-4c5d-6e7f8a9b0c1d',
            'b8c9d0e1-f2a3-4b4c-5d6e-7f8a9b0c1d2e',
            
            // Tree 3 (East Langka #3) - Flower 1 & 2
            'c9d0e1f2-a3b4-4c5d-6e7f-8a9b0c1d2e3f',
            'd0e1f2a3-b4c5-4d6e-7f8a-9b0c1d2e3f4a',
            'e1f2a3b4-c5d6-4e7f-8a9b-0c1d2e3f4a5b',
            'f2a3b4c5-d6e7-4f8a-9b0c-1d2e3f4a5b6c',
            
            // Tree 4 (East Langka #4) - Flower 1 & 2
            'a3b4c5d6-e7f8-4a9b-0c1d-2e3f4a5b6c7d',
            'b4c5d6e7-f8a9-4b0c-1d2e-3f4a5b6c7d8e',
            'c5d6e7f8-a9b0-4c1d-2e3f-4a5b6c7d8e9f',
            'd6e7f8a9-b0c1-4d2e-3f4a-5b6c7d8e9f0a',
            
            // Tree 5 (East Langka #5) - Flower 1 & 2
            'e7f8a9b0-c1d2-4e3f-4a5b-6c7d8e9f0a1b',
            'f8a9b0c1-d2e3-4f4a-5b6c-7d8e9f0a1b2c',
            'a9b0c1d2-e3f4-4a5b-6c7d-8e9f0a1b2c3d',
            'b0c1d2e3-f4a5-4b6c-7d8e-9f0a1b2c3d4e',
            
            // ===== NORTH LANGKA (Trees 6-10) =====
            // Tree 6 (North Langka #1) - Flower 1 & 2
            'c1d2e3f4-a5b6-4c7d-8e9f-0a1b2c3d4e5f',
            'd2e3f4a5-b6c7-4d8e-9f0a-1b2c3d4e5f6a',
            'e3f4a5b6-c7d8-4e9f-0a1b-2c3d4e5f6a7b',
            'f4a5b6c7-d8e9-4f0a-1b2c-3d4e5f6a7b8c',
            
            // Tree 7 (North Langka #2) - Flower 1 & 2
            'a5b6c7d8-e9f0-4a1b-2c3d-4e5f6a7b8c9d',
            'b6c7d8e9-f0a1-4b2c-3d4e-5f6a7b8c9d0e',
            'c7d8e9f0-a1b2-4c3d-4e5f-6a7b8c9d0e1f',
            'd8e9f0a1-b2c3-4d4e-5f6a-7b8c9d0e1f2a',
            
            // Tree 8 (North Langka #3) - Flower 1 & 2
            'e9f0a1b2-c3d4-4e5f-6a7b-8c9d0e1f2a3b',
            'f0a1b2c3-d4e5-4f6a-7b8c-9d0e1f2a3b4c',
            'a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5e',
            'b2c3d4e5-f6a7-4b8c-9d0e-1f2a3b4c5d6f',
            
            // Tree 9 (North Langka #4) - Flower 1 & 2
            'c3d4e5f6-a7b8-4c9d-0e1f-2a3b4c5d6e7a',
            'd4e5f6a7-b8c9-4d0e-1f2a-3b4c5d6e7f8b',
            'e5f6a7b8-c9d0-4e1f-2a3b-4c5d6e7f8a9c',
            'f6a7b8c9-d0e1-4f2a-3b4c-5d6e7f8a9b0d',
            
            // Tree 10 (North Langka #5) - Flower 1 & 2
            'a7b8c9d0-e1f2-4a3b-4c5d-6e7f8a9b0c1e',
            'b8c9d0e1-f2a3-4b4c-5d6e-7f8a9b0c1d2f',
            'c9d0e1f2-a3b4-4c5d-6e7f-8a9b0c1d2e3a',
            'd0e1f2a3-b4c5-4d6e-7f8a-9b0c1d2e3f4b',
            
            // ===== SOUTH LANGKA (Trees 11-15) =====
            // Tree 11 (South Langka #1) - Flower 1 & 2
            'e1f2a3b4-c5d6-4e7f-8a9b-0c1d2e3f4a5c',
            'f2a3b4c5-d6e7-4f8a-9b0c-1d2e3f4a5b6d',
            'a3b4c5d6-e7f8-4a9b-0c1d-2e3f4a5b6c7e',
            'b4c5d6e7-f8a9-4b0c-1d2e-3f4a5b6c7d8f',
            
            // Tree 12 (South Langka #2) - Flower 1 & 2
            'c5d6e7f8-a9b0-4c1d-2e3f-4a5b6c7d8e9a',
            'd6e7f8a9-b0c1-4d2e-3f4a-5b6c7d8e9f0b',
            'e7f8a9b0-c1d2-4e3f-4a5b-6c7d8e9f0a1c',
            'f8a9b0c1-d2e3-4f4a-5b6c-7d8e9f0a1b2d',
            
            // Tree 13 (South Langka #3) - Flower 1 & 2
            'a9b0c1d2-e3f4-4a5b-6c7d-8e9f0a1b2c3e',
            'b0c1d2e3-f4a5-4b6c-7d8e-9f0a1b2c3d4f',
            'c1d2e3f4-a5b6-4c7d-8e9f-0a1b2c3d4e5a',
            'd2e3f4a5-b6c7-4d8e-9f0a-1b2c3d4e5f6b',
            
            // Tree 14 (South Langka #4) - Flower 1 & 2
            'e3f4a5b6-c7d8-4e9f-0a1b-2c3d4e5f6a7c',
            'f4a5b6c7-d8e9-4f0a-1b2c-3d4e5f6a7b8d',
            'a5b6c7d8-e9f0-4a1b-2c3d-4e5f6a7b8c9e',
            'b6c7d8e9-f0a1-4b2c-3d4e-5f6a7b8c9d0f',
            
            // Tree 15 (South Langka #5) - Flower 1 & 2
            'c7d8e9f0-a1b2-4c3d-4e5f-6a7b8c9d0e1a',
            'd8e9f0a1-b2c3-4d4e-5f6a-7b8c9d0e1f2b',
            'e9f0a1b2-c3d4-4e5f-6a7b-8c9d0e1f2a3c',
            'f0a1b2c3-d4e5-4f6a-7b8c-9d0e1f2a3b4d',
            
            // ===== WEST LANGKA (Trees 16-20) =====
            // Tree 16 (West Langka #1) - Flower 1 & 2
            'a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5f',
            'b2c3d4e5-f6a7-4b8c-9d0e-1f2a3b4c5d6a',
            'c3d4e5f6-a7b8-4c9d-0e1f-2a3b4c5d6e7b',
            'd4e5f6a7-b8c9-4d0e-1f2a-3b4c5d6e7f8c',
            
            // Tree 17 (West Langka #2) - Flower 1 & 2
            'e5f6a7b8-c9d0-4e1f-2a3b-4c5d6e7f8a9d',
            'f6a7b8c9-d0e1-4f2a-3b4c-5d6e7f8a9b0e',
            'a7b8c9d0-e1f2-4a3b-4c5d-6e7f8a9b0c1f',
            'b8c9d0e1-f2a3-4b4c-5d6e-7f8a9b0c1d2a',
            
            // Tree 18 (West Langka #3) - Flower 1 & 2
            'c9d0e1f2-a3b4-4c5d-6e7f-8a9b0c1d2e3b',
            'd0e1f2a3-b4c5-4d6e-7f8a-9b0c1d2e3f4c',
            'e1f2a3b4-c5d6-4e7f-8a9b-0c1d2e3f4a5d',
            'f2a3b4c5-d6e7-4f8a-9b0c-1d2e3f4a5b6e',
            
            // Tree 19 (West Langka #4) - Flower 1 & 2
            'a3b4c5d6-e7f8-4a9b-0c1d-2e3f4a5b6c7f',
            'b4c5d6e7-f8a9-4b0c-1d2e-3f4a5b6c7d8a',
            'c5d6e7f8-a9b0-4c1d-2e3f-4a5b6c7d8e9b',
            'd6e7f8a9-b0c1-4d2e-3f4a-5b6c7d8e9f0c',
            
            // Tree 20 (West Langka #5) - Flower 1 & 2
            'e7f8a9b0-c1d2-4e3f-4a5b-6c7d8e9f0a1d',
            'f8a9b0c1-d2e3-4f4a-5b6c-7d8e9f0a1b2e',
            'a9b0c1d2-e3f4-4a5b-6c7d-8e9f0a1b2c3f',
            'b0c1d2e3-f4a5-4b6c-7d8e-9f0a1b2c3d4a',
        ];

        $fruitIndex = 0;
        $fruitNumber = 0; // Track overall fruit number for age distribution

        foreach ($flowers as $flower) {
            // Get the tree for display
            $tree = Tree::find($flower->tree_id);
            $treeDesc = $tree ? $tree->description : 'Unknown Tree';
            
            $this->command->info("📦 Processing flower from: {$treeDesc} (Flower quantity: {$flower->quantity})");
            
            // Track total fruit quantity for this flower
            $flowerFruitTotal = 0;
            $maxFruitPerFlower = $flower->quantity;
            
            // Create exactly 2 fruits per flower
            for ($i = 1; $i <= 2; $i++) {
                $fruitNumber++;
                
                // Determine age category based on fruit number (cycle through categories)
                $categoryIndex = ($fruitNumber % 3);
                $category = '';
                
                if ($categoryIndex == 0) {
                    $category = 'overdue';
                } elseif ($categoryIndex == 1) {
                    $category = 'ready';
                } else {
                    $category = 'approaching';
                }
                
                // Get random days based on category
                $daysAgo = rand($ageCategories[$category]['min'], $ageCategories[$category]['max']);
                $fruitCreatedAt = Carbon::now()->subDays($daysAgo);
                $baggedAt = clone $fruitCreatedAt;
                
                // Update category count
                $ageCategories[$category]['count']++;
                $ageCategories[$category]['total'] += $daysAgo;
                
                // Calculate fruit quantity based on remaining capacity
                $remainingCapacity = $maxFruitPerFlower - $flowerFruitTotal;
                
                if ($remainingCapacity <= 0) {
                    $fruitQuantity = 0;
                } elseif ($i == 2) {
                    $fruitQuantity = $remainingCapacity;
                } else {
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
                    
                    $statusIcon = $category === 'approaching' ? '🌱' : ($category === 'ready' ? '✅' : '⚠️');
                    $this->command->info("   {$statusIcon} Fruit {$i}: {$fruitQuantity} fruits (Running total: {$flowerFruitTotal}/{$maxFruitPerFlower})");
                    $this->command->info("      📅 Created: {$fruitCreatedAt->format('Y-m-d H:i:s')} ({$daysAgo} days ago) - {$category}");
                } else {
                    $this->command->info("   ⚠️  Fruit {$i}: No fruits (max capacity reached: {$flowerFruitTotal}/{$maxFruitPerFlower})");
                }
            }
            
            $totalFlowerQuantity += $flower->quantity;
            
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
        $this->command->info('');
        $this->command->info('📅 AGE DISTRIBUTION:');
        $this->command->info('   🌱 Approaching (115-119 days): ' . $ageCategories['approaching']['count'] . ' fruits');
        $this->command->info('   ✅ Ready (120-124 days): ' . $ageCategories['ready']['count'] . ' fruits');
        $this->command->info('   ⚠️  Overdue (125-130 days): ' . $ageCategories['overdue']['count'] . ' fruits');
        $this->command->info('====================================');
        $this->command->info('🖼️  Fruit Image: ' . $imageUrl);
        $this->command->info('====================================');
    }
}
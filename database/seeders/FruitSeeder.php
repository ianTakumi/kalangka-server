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
            'a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5e', // Changed last char from d to e
            'b2c3d4e5-f6a7-4b8c-9d0e-1f2a3b4c5d6f', // Changed last char from e to f
            
            // Tree 9 (North Langka #4) - Flower 1 & 2
            'c3d4e5f6-a7b8-4c9d-0e1f-2a3b4c5d6e7a', // Changed last char
            'd4e5f6a7-b8c9-4d0e-1f2a-3b4c5d6e7f8b', // Changed last char
            'e5f6a7b8-c9d0-4e1f-2a3b-4c5d6e7f8a9c', // Changed last char
            'f6a7b8c9-d0e1-4f2a-3b4c-5d6e7f8a9b0d', // Changed last char
            
            // Tree 10 (North Langka #5) - Flower 1 & 2
            'a7b8c9d0-e1f2-4a3b-4c5d-6e7f8a9b0c1e', // Changed last char
            'b8c9d0e1-f2a3-4b4c-5d6e-7f8a9b0c1d2f', // Changed last char
            'c9d0e1f2-a3b4-4c5d-6e7f-8a9b0c1d2e3a', // Changed last char
            'd0e1f2a3-b4c5-4d6e-7f8a-9b0c1d2e3f4b', // Changed last char
            
            // ===== SOUTH LANGKA (Trees 11-15) =====
            // Tree 11 (South Langka #1) - Flower 1 & 2
            'e1f2a3b4-c5d6-4e7f-8a9b-0c1d2e3f4a5c', // Changed last char
            'f2a3b4c5-d6e7-4f8a-9b0c-1d2e3f4a5b6d', // Changed last char
            'a3b4c5d6-e7f8-4a9b-0c1d-2e3f4a5b6c7e', // Changed last char
            'b4c5d6e7-f8a9-4b0c-1d2e-3f4a5b6c7d8f', // Changed last char
            
            // Tree 12 (South Langka #2) - Flower 1 & 2
            'c5d6e7f8-a9b0-4c1d-2e3f-4a5b6c7d8e9a', // Changed last char
            'd6e7f8a9-b0c1-4d2e-3f4a-5b6c7d8e9f0b', // Changed last char
            'e7f8a9b0-c1d2-4e3f-4a5b-6c7d8e9f0a1c', // Changed last char
            'f8a9b0c1-d2e3-4f4a-5b6c-7d8e9f0a1b2d', // Changed last char
            
            // Tree 13 (South Langka #3) - Flower 1 & 2
            'a9b0c1d2-e3f4-4a5b-6c7d-8e9f0a1b2c3e', // Changed last char
            'b0c1d2e3-f4a5-4b6c-7d8e-9f0a1b2c3d4f', // Changed last char
            'c1d2e3f4-a5b6-4c7d-8e9f-0a1b2c3d4e5a', // Changed last char
            'd2e3f4a5-b6c7-4d8e-9f0a-1b2c3d4e5f6b', // Changed last char
            
            // Tree 14 (South Langka #4) - Flower 1 & 2
            'e3f4a5b6-c7d8-4e9f-0a1b-2c3d4e5f6a7c', // Changed last char
            'f4a5b6c7-d8e9-4f0a-1b2c-3d4e5f6a7b8d', // Changed last char
            'a5b6c7d8-e9f0-4a1b-2c3d-4e5f6a7b8c9e', // Changed last char
            'b6c7d8e9-f0a1-4b2c-3d4e-5f6a7b8c9d0f', // Changed last char
            
            // Tree 15 (South Langka #5) - Flower 1 & 2
            'c7d8e9f0-a1b2-4c3d-4e5f-6a7b8c9d0e1a', // Changed last char
            'd8e9f0a1-b2c3-4d4e-5f6a-7b8c9d0e1f2b', // Changed last char
            'e9f0a1b2-c3d4-4e5f-6a7b-8c9d0e1f2a3c', // Changed last char
            'f0a1b2c3-d4e5-4f6a-7b8c-9d0e1f2a3b4d', // Changed last char
            
            // ===== WEST LANGKA (Trees 16-20) =====
            // Tree 16 (West Langka #1) - Flower 1 & 2
            'a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5f', // Changed last char from d to f
            'b2c3d4e5-f6a7-4b8c-9d0e-1f2a3b4c5d6a', // Changed last char
            'c3d4e5f6-a7b8-4c9d-0e1f-2a3b4c5d6e7b', // Changed last char
            'd4e5f6a7-b8c9-4d0e-1f2a-3b4c5d6e7f8c', // Changed last char
            
            // Tree 17 (West Langka #2) - Flower 1 & 2
            'e5f6a7b8-c9d0-4e1f-2a3b-4c5d6e7f8a9d', // Changed last char
            'f6a7b8c9-d0e1-4f2a-3b4c-5d6e7f8a9b0e', // Changed last char
            'a7b8c9d0-e1f2-4a3b-4c5d-6e7f8a9b0c1f', // Changed last char
            'b8c9d0e1-f2a3-4b4c-5d6e-7f8a9b0c1d2a', // Changed last char
            
            // Tree 18 (West Langka #3) - Flower 1 & 2
            'c9d0e1f2-a3b4-4c5d-6e7f-8a9b0c1d2e3b', // Changed last char
            'd0e1f2a3-b4c5-4d6e-7f8a-9b0c1d2e3f4c', // Changed last char
            'e1f2a3b4-c5d6-4e7f-8a9b-0c1d2e3f4a5d', // Changed last char
            'f2a3b4c5-d6e7-4f8a-9b0c-1d2e3f4a5b6e', // Changed last char
            
            // Tree 19 (West Langka #4) - Flower 1 & 2
            'a3b4c5d6-e7f8-4a9b-0c1d-2e3f4a5b6c7f', // Changed last char
            'b4c5d6e7-f8a9-4b0c-1d2e-3f4a5b6c7d8a', // Changed last char
            'c5d6e7f8-a9b0-4c1d-2e3f-4a5b6c7d8e9b', // Changed last char
            'd6e7f8a9-b0c1-4d2e-3f4a-5b6c7d8e9f0c', // Changed last char
            
            // Tree 20 (West Langka #5) - Flower 1 & 2
            'e7f8a9b0-c1d2-4e3f-4a5b-6c7d8e9f0a1d', // Changed last char
            'f8a9b0c1-d2e3-4f4a-5b6c-7d8e9f0a1b2e', // Changed last char
            'a9b0c1d2-e3f4-4a5b-6c7d-8e9f0a1b2c3f', // Changed last char
            'b0c1d2e3-f4a5-4b6c-7d8e-9f0a1b2c3d4a', // Changed last char
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
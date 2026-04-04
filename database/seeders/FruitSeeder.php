<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flower;
use App\Models\Tree;
use App\Models\Fruit;
use App\Models\User;
use Carbon\Carbon;

class FruitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all flowers with their relationships, exclude admin users
        $flowers = Flower::with(['tree', 'user'])
            ->whereHas('user', function ($query) {
                $query->where('role', '!=', 'admin');
            })
            ->get();
        
        if ($flowers->isEmpty()) {
            $this->command->error('⚠️  No flowers found for non-admin users. Please run FlowerSeeder first.');
            return;
        }

        $this->command->info('====================================');
        $this->command->info('🍎 Creating fruits for ' . $flowers->count() . ' flowers...');
        $this->command->info('🚫 Excluding admin users from fruit records');
        $this->command->info('====================================');

        // Fruit image URL
        $imageUrl = 'https://gujmgaqntmdvqvvlwqhf.supabase.co/storage/v1/object/public/kalangka/fruits/samplefruit.jpg';
        
        $totalFruits = 0;
        $totalFlowerQuantity = 0;
        $totalFruitQuantity = 0;
        
        // Track fruits per user
        $userFruitCount = [];
        $userFruitQuantity = [];
        
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

        // Get all non-admin users from flowers and initialize tracking
        $users = $flowers->pluck('user')->unique('id')->filter(function ($user) {
            return $user && $user->role !== 'admin';
        });
        
        $this->command->info('👥 Non-admin users with flowers: ' . $users->count());
        
        foreach ($users as $user) {
            $userFruitCount[$user->id] = 0;
            $userFruitQuantity[$user->id] = 0;
        }

        // Count admin users that were excluded
        $adminFlowers = Flower::whereHas('user', function ($query) {
            $query->where('role', 'admin');
        })->count();
        
        if ($adminFlowers > 0) {
            $this->command->warn('⚠️  Skipped ' . $adminFlowers . ' flowers belonging to admin users');
        }

        // Generate fruit IDs for each flower (2 fruits per flower)
        $fruitIds = $this->generateFruitIds($flowers->count() * 2);
        
        $fruitIndex = 0;
        $fruitNumber = 0;
        
        // Start with tag_id = 1
        $currentTagId = 1;
        
        $this->command->info('🏷️  tag_id will cycle: 1,2,3,4,1,2,3,4,...');
        $this->command->info('');

        foreach ($flowers as $flower) {
            // Get the tree and user for display
            $tree = $flower->tree;
            $user = $flower->user;
            
            // Skip if user is admin (double check)
            if ($user && $user->role === 'admin') {
                $this->command->warn("   ⚠️  Skipping admin user: {$user->first_name} {$user->last_name}");
                continue;
            }
            
            $treeDesc = $tree ? $tree->description : 'Unknown Tree';
            $userName = $user ? $user->first_name . ' ' . $user->last_name : 'Unknown User';
            
            $this->command->info("📦 Processing flower from: {$treeDesc}");
            $this->command->info("   👤 User: {$userName} (Role: {$user->role})");
            $this->command->info("   🌸 Flower quantity: {$flower->quantity}");
            
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
                
                if ($fruitQuantity > 0 && $fruitIndex < count($fruitIds)) {
                    // Assign tag_id (cycles 1-4)
                    $tagId = $currentTagId;
                    
                    Fruit::create([
                        'id' => $fruitIds[$fruitIndex++],
                        'flower_id' => $flower->id,
                        'tree_id' => $flower->tree_id,
                        'user_id' => $flower->user_id,
                        'quantity' => $fruitQuantity,
                        'tag_id' => $tagId,
                        'bagged_at' => $baggedAt,
                        'image_url' => $imageUrl,
                        'created_at' => $fruitCreatedAt,
                        'updated_at' => $fruitCreatedAt,
                    ]);
                    
                    $flowerFruitTotal += $fruitQuantity;
                    $totalFruits++;
                    $totalFruitQuantity += $fruitQuantity;
                    
                    // Update user tracking
                    $userFruitCount[$flower->user_id]++;
                    $userFruitQuantity[$flower->user_id] += $fruitQuantity;
                    
                    $statusIcon = $category === 'approaching' ? '🌱' : ($category === 'ready' ? '✅' : '⚠️');
                    $this->command->info("   {$statusIcon} Fruit {$i}: {$fruitQuantity} fruits (Running total: {$flowerFruitTotal}/{$maxFruitPerFlower})");
                    $this->command->info("      📅 Created: {$fruitCreatedAt->format('Y-m-d H:i:s')} ({$daysAgo} days ago) - {$category}");
                    $this->command->info("      🏷️  Batch # (tag_id): {$tagId}");
                    $this->command->info("      👤 Assigned to user: {$userName}");
                    
                    // Increment tag_id and reset to 1 if exceeds 4
                    $currentTagId++;
                    if ($currentTagId > 4) {
                        $currentTagId = 1;
                    }
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

        // Check for users without fruits (non-admin only)
        $usersWithoutFruits = [];
        foreach ($users as $user) {
            if ($userFruitCount[$user->id] == 0) {
                $usersWithoutFruits[] = $user;
            }
        }
        
        // Create additional fruits for users without any fruits
        if (!empty($usersWithoutFruits)) {
            $this->command->info('====================================');
            $this->command->info('🔍 Found ' . count($usersWithoutFruits) . ' non-admin user(s) without fruit records!');
            $this->command->info('📝 Creating additional fruits for these users...');
            $this->command->info('====================================');
            
            foreach ($usersWithoutFruits as $user) {
                // Find a flower belonging to this user
                $userFlower = $flowers->where('user_id', $user->id)->first();
                
                if ($userFlower) {
                    $this->command->info("📦 Creating fruit for user: {$user->first_name} {$user->last_name}");
                    
                    // Create 2 fruits for this user to ensure they have records
                    for ($j = 1; $j <= 2; $j++) {
                        $daysAgo = rand(115, 130);
                        $fruitCreatedAt = Carbon::now()->subDays($daysAgo);
                        $baggedAt = clone $fruitCreatedAt;
                        
                        // Determine category for age
                        if ($daysAgo >= 125) {
                            $category = 'overdue';
                        } elseif ($daysAgo >= 120) {
                            $category = 'ready';
                        } else {
                            $category = 'approaching';
                        }
                        
                        $fruitQuantity = rand(1, 3);
                        
                        // Assign current tag_id (cycles 1-4)
                        $tagId = $currentTagId;
                        
                        Fruit::create([
                            'id' => (string) \Illuminate\Support\Str::uuid(),
                            'flower_id' => $userFlower->id,
                            'tree_id' => $userFlower->tree_id,
                            'user_id' => $user->id,
                            'quantity' => $fruitQuantity,
                            'tag_id' => $tagId,
                            'bagged_at' => $baggedAt,
                            'image_url' => $imageUrl,
                            'created_at' => $fruitCreatedAt,
                            'updated_at' => $fruitCreatedAt,
                        ]);
                        
                        $totalFruits++;
                        $totalFruitQuantity += $fruitQuantity;
                        $userFruitCount[$user->id]++;
                        $userFruitQuantity[$user->id] += $fruitQuantity;
                        
                        $statusIcon = $category === 'approaching' ? '🌱' : ($category === 'ready' ? '✅' : '⚠️');
                        $this->command->info("   {$statusIcon} Fruit {$j}: {$fruitQuantity} fruits");
                        $this->command->info("      📅 Created: {$fruitCreatedAt->format('Y-m-d H:i:s')} ({$daysAgo} days ago) - {$category}");
                        $this->command->info("      🏷️  Batch # (tag_id): {$tagId}");
                        
                        // Increment tag_id and reset to 1 if exceeds 4
                        $currentTagId++;
                        if ($currentTagId > 4) {
                            $currentTagId = 1;
                        }
                    }
                    $this->command->info("   ✅ User now has fruit records!");
                    $this->command->info('');
                }
            }
        }

        // Display user distribution summary (non-admin only)
        $this->command->info('====================================');
        $this->command->info('👥 FRUIT DISTRIBUTION BY USER (Non-Admin Only):');
        $this->command->info('====================================');
        
        foreach ($users as $user) {
            $fruitCount = $userFruitCount[$user->id] ?? 0;
            $fruitQty = $userFruitQuantity[$user->id] ?? 0;
            $status = $fruitCount > 0 ? '✅' : '⚠️';
            $this->command->info("   {$status} {$user->first_name} {$user->last_name} ({$user->email}):");
            $this->command->info("      📦 Fruit records: {$fruitCount}");
            $this->command->info("      🍎 Total fruits: {$fruitQty}");
        }

        // Count how many fruits per tag_id
        $tagIdCounts = [];
        for ($i = 1; $i <= 4; $i++) {
            $tagIdCounts[$i] = Fruit::where('tag_id', $i)->count();
        }

        $this->command->info('');
        $this->command->info('====================================');
        $this->command->info('🍎 FINAL SUMMARY');
        $this->command->info('====================================');
        $this->command->info('🌸 Total flowers (non-admin): ' . $flowers->count());
        $this->command->info('📊 Total flower quantity: ' . $totalFlowerQuantity);
        $this->command->info('🍎 Total fruit records created: ' . $totalFruits);
        $this->command->info('📊 Total fruit quantity: ' . $totalFruitQuantity);
        $this->command->info('👥 Non-admin users with fruit records: ' . count(array_filter($userFruitCount, function($count) { return $count > 0; })) . '/' . $users->count());
        $this->command->info('📈 Utilization rate: ' . round(($totalFruitQuantity / max($totalFlowerQuantity, 1)) * 100, 2) . '%');
        $this->command->info('');
        $this->command->info('📅 AGE DISTRIBUTION:');
        $this->command->info('   🌱 Approaching (115-119 days): ' . $ageCategories['approaching']['count'] . ' fruits');
        $this->command->info('   ✅ Ready (120-124 days): ' . $ageCategories['ready']['count'] . ' fruits');
        $this->command->info('   ⚠️  Overdue (125-130 days): ' . $ageCategories['overdue']['count'] . ' fruits');
        $this->command->info('');
        $this->command->info('🏷️  BATCH DISTRIBUTION (tag_id 1-4):');
        foreach ($tagIdCounts as $tagId => $count) {
            $this->command->info("   • Batch {$tagId}: {$count} fruit records");
        }
        $this->command->info('====================================');
        $this->command->info('🚫 Admin users are excluded from fruit records');
        $this->command->info('🖼️  Fruit Image: ' . $imageUrl);
        $this->command->info('====================================');
    }
    
    /**
     * Generate fruit IDs for each flower
     */
    private function generateFruitIds($totalFruitsNeeded)
    {
        $fruitIds = [];
        for ($i = 0; $i < $totalFruitsNeeded; $i++) {
            $fruitIds[] = (string) \Illuminate\Support\Str::uuid();
        }
        return $fruitIds;
    }
}
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
        
        // Helper function to get random date from January 2026 to current month
        $getRandomDate = function() {
            $startDate = Carbon::create(2026, 1, 1); // January 1, 2026
            $endDate = Carbon::now(); // Current date
            
            // If start date is in the future (should not happen), use current date minus 30 days
            if ($startDate->gt($endDate)) {
                $startDate = Carbon::now()->subMonths(6);
            }
            
            // Get random timestamp between start and end date
            $randomTimestamp = rand($startDate->timestamp, $endDate->timestamp);
            $randomDate = Carbon::createFromTimestamp($randomTimestamp);
            
            // Set random time within the day
            $randomDate->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
            
            return $randomDate;
        };
        
        $totalFruits = 0;
        $totalFlowerQuantity = 0;
        $totalFruitQuantity = 0;
        
        // Track fruits per user
        $userFruitCount = [];
        $userFruitQuantity = [];
        $userFruitDates = []; // Track dates per user for summary
        
        // Get all non-admin users from flowers and initialize tracking
        $users = $flowers->pluck('user')->unique('id')->filter(function ($user) {
            return $user && $user->role !== 'admin';
        });
        
        $this->command->info('👥 Non-admin users with flowers: ' . $users->count());
        $this->command->info('📅 Date range: January 2026 to ' . Carbon::now()->format('F Y'));
        $this->command->info('');
        
        foreach ($users as $user) {
            $userFruitCount[$user->id] = 0;
            $userFruitQuantity[$user->id] = 0;
            $userFruitDates[$user->id] = [];
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
                // Get random date from January 2026 to current
                $fruitCreatedAt = $getRandomDate();
                $baggedAt = clone $fruitCreatedAt;
                
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
                    $userFruitDates[$flower->user_id][] = $fruitCreatedAt->format('Y-m-d');
                    
                    $this->command->info("   🍎 Fruit {$i}: {$fruitQuantity} fruits (Running total: {$flowerFruitTotal}/{$maxFruitPerFlower})");
                    $this->command->info("      📅 Date: {$fruitCreatedAt->format('F j, Y')} ({$fruitCreatedAt->diffForHumans()})");
                    $this->command->info("      🏷️  Batch # (tag_id): {$tagId}");
                    $this->command->info("      👤 Assigned to user: {$userName}");
                } else {
                    $this->command->info("   ⚠️  Fruit {$i}: No fruits (max capacity reached: {$flowerFruitTotal}/{$maxFruitPerFlower})");
                }
                
                // Increment tag_id and reset to 1 if exceeds 4
                $currentTagId++;
                if ($currentTagId > 4) {
                    $currentTagId = 1;
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
                        $fruitCreatedAt = $getRandomDate();
                        $baggedAt = clone $fruitCreatedAt;
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
                        $userFruitDates[$user->id][] = $fruitCreatedAt->format('Y-m-d');
                        
                        $this->command->info("   🍎 Fruit {$j}: {$fruitQuantity} fruits");
                        $this->command->info("      📅 Date: {$fruitCreatedAt->format('F j, Y')} ({$fruitCreatedAt->diffForHumans()})");
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
            $dateRange = !empty($userFruitDates[$user->id]) 
                ? ' (' . min($userFruitDates[$user->id]) . ' to ' . max($userFruitDates[$user->id]) . ')' 
                : '';
            $this->command->info("   {$status} {$user->first_name} {$user->last_name} ({$user->email}):");
            $this->command->info("      📦 Fruit records: {$fruitCount}");
            $this->command->info("      🍎 Total fruits: {$fruitQty}");
            if ($dateRange) {
                $this->command->info("      📅 Date range:{$dateRange}");
            }
        }

        // Count how many fruits per tag_id
        $tagIdCounts = [];
        for ($i = 1; $i <= 4; $i++) {
            $tagIdCounts[$i] = Fruit::where('tag_id', $i)->count();
        }

        // Get overall date range statistics
        $oldestFruit = Fruit::orderBy('created_at', 'asc')->first();
        $newestFruit = Fruit::orderBy('created_at', 'desc')->first();

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
        $this->command->info('📅 DATE RANGE STATISTICS:');
        if ($oldestFruit && $newestFruit) {
            $this->command->info("   🍎 Oldest fruit date: " . $oldestFruit->created_at->format('F j, Y'));
            $this->command->info("   🍎 Newest fruit date: " . $newestFruit->created_at->format('F j, Y'));
            $this->command->info("   📊 Timespan: " . $oldestFruit->created_at->diffForHumans($newestFruit->created_at, true));
        }
        $this->command->info('');
        $this->command->info('🏷️  BATCH DISTRIBUTION (tag_id 1-4):');
        foreach ($tagIdCounts as $tagId => $count) {
            $percentage = $totalFruits > 0 ? round(($count / $totalFruits) * 100, 2) : 0;
            $this->command->info("   • Batch {$tagId}: {$count} fruit records ({$percentage}%)");
        }
        $this->command->info('====================================');
        $this->command->info('🚫 Admin users are excluded from fruit records');
        $this->command->info('🖼️  Fruit Image: ' . $imageUrl);
        $this->command->info('📅 All dates are randomized from January 2026 to ' . Carbon::now()->format('F Y'));
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
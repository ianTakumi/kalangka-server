<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flower;
use App\Models\Tree;
use App\Models\User;
use Carbon\Carbon;

class FlowerSeeder extends Seeder
{
    public function run(): void
    {
        // Flower::truncate();

        $trees = Tree::all();
        // Only get users with role NOT equal to 'admin'
        $users = User::where('role', '!=', 'admin')->get();
        
        if ($trees->isEmpty()) {
            $this->command->error('⚠️  No trees found. Please run TreeSeeder first.');
            return;
        }
        
        if ($users->isEmpty()) {
            $this->command->error('⚠️  No non-admin users found. Please create regular users first.');
            return;
        }

        $this->command->info('====================================');
        $this->command->info('🌸 Creating flowers for ' . $trees->count() . ' trees...');
        $this->command->info('👥 Available non-admin users: ' . $users->count());
        $this->command->info('====================================');

        // Array of 5 flower image URLs to randomize
        $flowerImageUrls = [
            'https://ugigkldnrrwsxdshpxjz.supabase.co/storage/v1/object/public/kalangka/flowers/flower%201.png',
            'https://ugigkldnrrwsxdshpxjz.supabase.co/storage/v1/object/public/kalangka/flowers/flower%202.png',
            'https://ugigkldnrrwsxdshpxjz.supabase.co/storage/v1/object/public/kalangka/flowers/flower%203.png',
            'https://ugigkldnrrwsxdshpxjz.supabase.co/storage/v1/object/public/kalangka/flowers/flower%204.png',
            'https://ugigkldnrrwsxdshpxjz.supabase.co/storage/v1/object/public/kalangka/flowers/flower%205.png',
        ];

        // Helper function to get a random flower image URL
        $getRandomFlowerImage = function() use ($flowerImageUrls) {
            return $flowerImageUrls[array_rand($flowerImageUrls)];
        };
        
        $totalFlowers = 0;

        // HARDCODED REAL UUIDs for flowers (2 per tree = 134 flowers total for 67 trees)
        $flowerIds = [
            // J1
            '1d92e857-045d-44f4-935c-61a5f0637881',
            '2a3f8b9c-4d5e-6f7a-8b9c-0d1e2f3a4b5c',
            // J2
            '3b4c5d6e-7f8a-9b0c-1d2e-3f4a5b6c7d8e',
            '4c5d6e7f-8a9b-0c1d-2e3f-4a5b6c7d8e9f',
            // J3
            '5d6e7f8a-9b0c-1d2e-3f4a-5b6c7d8e9f0a',
            '6e7f8a9b-0c1d-2e3f-4a5b-6c7d8e9f0a1b',
            // J4
            '7f8a9b0c-1d2e-3f4a-5b6c-7d8e9f0a1b2c',
            '8a9b0c1d-2e3f-4a5b-6c7d-8e9f0a1b2c3d',
            // J5
            '9b0c1d2e-3f4a-5b6c-7d8e-9f0a1b2c3d4e',
            '0c1d2e3f-4a5b-6c7d-8e9f-0a1b2c3d4e5f',
            // J6
            '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d',
            '2b3c4d5e-6f7a-8b9c-0d1e-2f3a4b5c6d7e',
            // J7
            '3c4d5e6f-7a8b-9c0d-1e2f-3a4b5c6d7e8f',
            '4d5e6f7a-8b9c-0d1e-2f3a-4b5c6d7e8f9a',
            // J8
            '5e6f7a8b-9c0d-1e2f-3a4b-5c6d7e8f9a0b',
            '6f7a8b9c-0d1e-2f3a-4b5c-6d7e8f9a0b1c',
            // J9
            '7a8b9c0d-1e2f-3a4b-5c6d-7e8f9a0b1c2d',
            '8b9c0d1e-2f3a-4b5c-6d7e-8f9a0b1c2d3e',
            // J10
            '9c0d1e2f-3a4b-5c6d-7e8f-9a0b1c2d3e4f',
            '0d1e2f3a-4b5c-6d7e-8f9a-0b1c2d3e4f5a',
            // J11
            '1e2f3a4b-5c6d-7e8f-9a0b-1c2d3e4f5a6b',
            '2f3a4b5c-6d7e-8f9a-0b1c-2d3e4f5a6b7c',
            // J12
            '3a4b5c6d-7e8f-9a0b-1c2d-3e4f5a6b7c8d',
            '4b5c6d7e-8f9a-0b1c-2d3e-4f5a6b7c8d9e',
            // J13
            '5c6d7e8f-9a0b-1c2d-3e4f-5a6b7c8d9e0f',
            '6d7e8f9a-0b1c-2d3e-4f5a-6b7c8d9e0f1a',
            // J14
            '7e8f9a0b-1c2d-3e4f-5a6b-7c8d9e0f1a2b',
            '8f9a0b1c-2d3e-4f5a-6b7c-8d9e0f1a2b3c',
            // J15
            '9a0b1c2d-3e4f-5a6b-7c8d-9e0f1a2b3c4d',
            '0b1c2d3e-4f5a-6b7c-8d9e-0f1a2b3c4d5e',
            // J16
            '1c2d3e4f-5a6b-7c8d-9e0f-1a2b3c4d5e6f',
            '2d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7a',
            // J21
            '3e4f5a6b-7c8d-9e0f-1a2b-3c4d5e6f7a8b',
            '4f5a6b7c-8d9e-0f1a-2b3c-4d5e6f7a8b9c',
            // J22
            '5a6b7c8d-9e0f-1a2b-3c4d-5e6f7a8b9c0d',
            '6b7c8d9e-0f1a-2b3c-4d5e-6f7a8b9c0d1e',
            // J23
            '7c8d9e0f-1a2b-3c4d-5e6f-7a8b9c0d1e2f',
            '8d9e0f1a-2b3c-4d5e-6f7a-8b9c0d1e2f3a',
            // J25
            '9e0f1a2b-3c4d-5e6f-7a8b-9c0d1e2f3a4b',
            '0f1a2b3c-4d5e-6f7a-8b9c-0d1e2f3a4b5c',
            // J26
            '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6e',
            '2b3c4d5e-6f7a-8b9c-0d1e-2f3a4b5c6d7f',
            // J27
            '3c4d5e6f-7a8b-9c0d-1e2f-3a4b5c6d7e8g',
            '4d5e6f7a-8b9c-0d1e-2f3a-4b5c6d7e8f9b',
            // J28
            '5e6f7a8b-9c0d-1e2f-3a4b-5c6d7e8f9a0c',
            '6f7a8b9c-0d1e-2f3a-4b5c-6d7e8f9a0b1d',
            // J30
            '7a8b9c0d-1e2f-3a4b-5c6d-7e8f9a0b1c2e',
            '8b9c0d1e-2f3a-4b5c-6d7e-8f9a0b1c2d3f',
            // J31
            '9c0d1e2f-3a4b-5c6d-7e8f-9a0b1c2d3e4a',
            '0d1e2f3a-4b5c-6d7e-8f9a-0b1c2d3e4f5b',
            // J32
            '1e2f3a4b-5c6d-7e8f-9a0b-1c2d3e4f5a6c',
            '2f3a4b5c-6d7e-8f9a-0b1c-2d3e4f5a6b7d',
            // J33
            '3a4b5c6d-7e8f-9a0b-1c2d-3e4f5a6b7c8e',
            '4b5c6d7e-8f9a-0b1c-2d3e-4f5a6b7c8d9f',
            // J34
            '5c6d7e8f-9a0b-1c2d-3e4f-5a6b7c8d9e0a',
            '6d7e8f9a-0b1c-2d3e-4f5a-6b7c8d9e0f1b',
            // J39
            '7e8f9a0b-1c2d-3e4f-5a6b-7c8d9e0f1a2c',
            '8f9a0b1c-2d3e-4f5a-6b7c-8d9e0f1a2b3d',
            // J40
            '9a0b1c2d-3e4f-5a6b-7c8d-9e0f1a2b3c4e',
            '0b1c2d3e-4f5a-6b7c-8d9e-0f1a2b3c4d5f',
            // J41
            '1c2d3e4f-5a6b-7c8d-9e0f-1a2b3c4d5e6a',
            '2d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7b',
            // J42
            '3e4f5a6b-7c8d-9e0f-1a2b-3c4d5e6f7a8c',
            '4f5a6b7c-8d9e-0f1a-2b3c-4d5e6f7a8b9d',
            // J43
            '5a6b7c8d-9e0f-1a2b-3c4d-5e6f7a8b9c0e',
            '6b7c8d9e-0f1a-2b3c-4d5e-6f7a8b9c0d1f',
            // J45
            '7c8d9e0f-1a2b-3c4d-5e6f-7a8b9c0d1e2a',
            '8d9e0f1a-2b3c-4d5e-6f7a-8b9c0d1e2f3b',
            // J46
            '9e0f1a2b-3c4d-5e6f-7a8b-9c0d1e2f3a4c',
            '0f1a2b3c-4d5e-6f7a-8b9c-0d1e2f3a4b5d',
            // J47
            '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6f',
            '2b3c4d5e-6f7a-8b9c-0d1e-2f3a4b5c6d7a',
            // J48
            '3c4d5e6f-7a8b-9c0d-1e2f-3a4b5c6d7e8h',
            '4d5e6f7a-8b9c-0d1e-2f3a-4b5c6d7e8f9c',
            // J49
            '5e6f7a8b-9c0d-1e2f-3a4b-5c6d7e8f9a0d',
            '6f7a8b9c-0d1e-2f3a-4b5c-6d7e8f9a0b1e',
            // J57
            '7a8b9c0d-1e2f-3a4b-5c6d-7e8f9a0b1c2f',
            '8b9c0d1e-2f3a-4b5c-6d7e-8f9a0b1c2d3a',
            // J58
            '9c0d1e2f-3a4b-5c6d-7e8f-9a0b1c2d3e4b',
            '0d1e2f3a-4b5c-6d7e-8f9a-0b1c2d3e4f5c',
            // J60
            '1e2f3a4b-5c6d-7e8f-9a0b-1c2d3e4f5a6d',
            '2f3a4b5c-6d7e-8f9a-0b1c-2d3e4f5a6b7e',
            // J83
            '3a4b5c6d-7e8f-9a0b-1c2d-3e4f5a6b7c8f',
            '4b5c6d7e-8f9a-0b1c-2d3e-4f5a6b7c8d9a',
            // J90
            '5c6d7e8f-9a0b-1c2d-3e4f-5a6b7c8d9e0b',
            '6d7e8f9a-0b1c-2d3e-4f5a-6b7c8d9e0f1c',
            // J95
            '7e8f9a0b-1c2d-3e4f-5a6b-7c8d9e0f1a2d',
            '8f9a0b1c-2d3e-4f5a-6b7c-8d9e0f1a2b3e',
            // J96
            '9a0b1c2d-3e4f-5a6b-7c8d-9e0f1a2b3c4f',
            '0b1c2d3e-4f5a-6b7c-8d9e-0f1a2b3c4d5a',
            // J97
            '1c2d3e4f-5a6b-7c8d-9e0f-1a2b3c4d5e6b',
            '2d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7c',
            // J101
            '3e4f5a6b-7c8d-9e0f-1a2b-3c4d5e6f7a8d',
            '4f5a6b7c-8d9e-0f1a-2b3c-4d5e6f7a8b9e',
            // J113
            '5a6b7c8d-9e0f-1a2b-3c4d-5e6f7a8b9c0f',
            '6b7c8d9e-0f1a-2b3c-4d5e-6f7a8b9c0d1a',
            // J124
            '7c8d9e0f-1a2b-3c4d-5e6f-7a8b9c0d1e2b',
            '8d9e0f1a-2b3c-4d5e-6f7a-8b9c0d1e2f3c',
            // J125
            '9e0f1a2b-3c4d-5e6f-7a8b-9c0d1e2f3a4d',
            '0f1a2b3c-4d5e-6f7a-8b9c-0d1e2f3a4b5e',
            // J126
            '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6a',
            '2b3c4d5e-6f7a-8b9c-0d1e-2f3a4b5c6d7b',
            // J127
            '3c4d5e6f-7a8b-9c0d-1e2f-3a4b5c6d7e8i',
            '4d5e6f7a-8b9c-0d1e-2f3a-4b5c6d7e8f9d',
            // J128
            '5e6f7a8b-9c0d-1e2f-3a4b-5c6d7e8f9a0e',
            '6f7a8b9c-0d1e-2f3a-4b5c-6d7e8f9a0b1f',
            // J135
            '7a8b9c0d-1e2f-3a4b-5c6d-7e8f9a0b1c2a',
            '8b9c0d1e-2f3a-4b5c-6d7e-8f9a0b1c2d3b',
            // J140
            '9c0d1e2f-3a4b-5c6d-7e8f-9a0b1c2d3e4c',
            '0d1e2f3a-4b5c-6d7e-8f9a-0b1c2d3e4f5d',
            // J141
            '1e2f3a4b-5c6d-7e8f-9a0b-1c2d3e4f5a6e',
            '2f3a4b5c-6d7e-8f9a-0b1c-2d3e4f5a6b7f',
            // J147
            '3a4b5c6d-7e8f-9a0b-1c2d-3e4f5a6b7c8a',
            '4b5c6d7e-8f9a-0b1c-2d3e-4f5a6b7c8d9b',
            // J148
            '5c6d7e8f-9a0b-1c2d-3e4f-5a6b7c8d9e0c',
            '6d7e8f9a-0b1c-2d3e-4f5a-6b7c8d9e0f1d',
            // J151
            '7e8f9a0b-1c2d-3e4f-5a6b-7c8d9e0f1a2e',
            '8f9a0b1c-2d3e-4f5a-6b7c-8d9e0f1a2b3f',
            // J154
            '9a0b1c2d-3e4f-5a6b-7c8d-9e0f1a2b3c4a',
            '0b1c2d3e-4f5a-6b7c-8d9e-0f1a2b3c4d5b',
            // J155
            '1c2d3e4f-5a6b-7c8d-9e0f-1a2b3c4d5e6c',
            '2d3e4f5a-6b7c-8d9e-0f1a-2b3c4d5e6f7d',
            // J159
            '3e4f5a6b-7c8d-9e0f-1a2b-3c4d5e6f7a8e',
            '4f5a6b7c-8d9e-0f1a-2b3c-4d5e6f7a8b9f',
            // J160
            '5a6b7c8d-9e0f-1a2b-3c4d-5e6f7a8b9c0a',
            '6b7c8d9e-0f1a-2b3c-4d5e-6f7a8b9c0d1b',
            // J200
            '7c8d9e0f-1a2b-3c4d-5e6f-7a8b9c0d1e2c',
            '8d9e0f1a-2b3c-4d5e-6f7a-8b9c0d1e2f3d',
            // J201
            '9e0f1a2b-3c4d-5e6f-7a8b-9c0d1e2f3a4e',
            '0f1a2b3c-4d5e-6f7a-8b9c-0d1e2f3a4b5f',
            // J202
            '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6b',
            '2b3c4d5e-6f7a-8b9c-0d1e-2f3a4b5c6d7c',
        ];

      
        $getRandomDate = function() {
            // Date between 110 and 120 days ago from today
            $daysAgo = rand(110, 120);
            $randomDate = Carbon::now()->subDays($daysAgo);
            
            // Set random time within the day
            $randomDate->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
            
            return $randomDate;
        };

        $flowerIndex = 0;
        $userIds = $users->pluck('id')->toArray();
        $userCount = count($userIds);
        
        // Ensure each non-admin user gets at least one flower
        $this->command->info('👥 Ensuring each non-admin user gets at least one flower...');
        
        // First, create one flower for each non-admin user
        foreach ($users as $userIndex => $user) {
            // Get a tree for this user (cycle through trees)
            $tree = $trees[$userIndex % $trees->count()];
            
            // Random date from January to current month
            $randomDate = $getRandomDate();
            
            // Get random flower image
            $randomImage = $getRandomFlowerImage();
            
            Flower::create([
                'id' => $flowerIds[$flowerIndex++],
                'tree_id' => $tree->id,
                'user_id' => $user->id,
                'quantity' => rand(1, 5),
                'wrapped_at' => $randomDate,
                'image_url' => $randomImage,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
            
            $totalFlowers++;
            $this->command->info("   ✅ Created flower for user: {$user->first_name} {$user->last_name} (Role: {$user->role}) | Image: flower " . (array_search($randomImage, $flowerImageUrls) + 1) . ".png");
        }
        
        // Now create remaining flowers (2 per tree total, so calculate remaining)
        $flowersPerTree = 2;
        $targetTotalFlowers = $trees->count() * $flowersPerTree;
        $remainingFlowers = $targetTotalFlowers - $totalFlowers;
        
        if ($remainingFlowers > 0) {
            $this->command->info("📦 Creating remaining {$remainingFlowers} flowers...");
            
            foreach ($trees as $tree) {
                // Count how many flowers this tree already has
                $existingFlowersCount = Flower::where('tree_id', $tree->id)->count();
                $neededForTree = $flowersPerTree - $existingFlowersCount;
                
                for ($i = 1; $i <= $neededForTree; $i++) {
                    if ($flowerIndex >= count($flowerIds)) {
                        $this->command->error('⚠️  Not enough flower IDs!');
                        break 2;
                    }
                    
                    // Randomly assign a non-admin user (cycle through users)
                    $randomUser = $users[$flowerIndex % $userCount];
                    
                    // Random date from January to current month
                    $randomDate = $getRandomDate();
                    
                    // Get random flower image
                    $randomImage = $getRandomFlowerImage();
                    
                    Flower::create([
                        'id' => $flowerIds[$flowerIndex++],
                        'tree_id' => $tree->id,
                        'user_id' => $randomUser->id,
                        'quantity' => rand(1, 5),
                        'wrapped_at' => $randomDate,
                        'image_url' => $randomImage,
                        'created_at' => $randomDate,
                        'updated_at' => $randomDate,
                    ]);
                    
                    $totalFlowers++;
                }
            }
        }
        
        $this->command->info('====================================');
        $this->command->info('🌸 TOTAL FLOWERS CREATED: ' . $totalFlowers);
        $this->command->info('====================================');
        
        // Display user flower distribution (only non-admin users)
        $this->command->info('📊 Non-Admin User Flower Distribution:');
        foreach ($users as $user) {
            $flowerCount = Flower::where('user_id', $user->id)->count();
            $this->command->info("   👤 {$user->first_name} {$user->last_name} (Role: {$user->role}): {$flowerCount} flower(s)");
        }
        
        // Display date range statistics
        $this->command->info('====================================');
        $this->command->info('📅 Date Range Statistics:');
        
        $oldestFlower = Flower::orderBy('wrapped_at', 'asc')->first();
        $newestFlower = Flower::orderBy('wrapped_at', 'desc')->first();
        
        if ($oldestFlower && $newestFlower) {
            $this->command->info("   📅 Oldest flower date: " . $oldestFlower->wrapped_at->format('F j, Y'));
            $this->command->info("   📅 Newest flower date: " . $newestFlower->wrapped_at->format('F j, Y'));
        }
        
        // Display flower image URL distribution
        $this->command->info('====================================');
        $this->command->info('🖼️  Flower Image Distribution:');
        foreach ($flowerImageUrls as $index => $imageUrl) {
            $imageCount = Flower::where('image_url', $imageUrl)->count();
            $flowerNumber = $index + 1;
            $this->command->info("   🌺 flower {$flowerNumber}.png: {$imageCount} flower(s)");
        }
        
        $this->command->info('====================================');
        
        // Show first few IDs as sample
        $this->command->info('📋 Sample Flower IDs:');
        $this->command->info('   ' . $flowerIds[0]);
        $this->command->info('   ' . $flowerIds[1]);
        $this->command->info('   ' . $flowerIds[2]);
        $this->command->info('====================================');
    }
}
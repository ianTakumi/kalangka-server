<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tree;
use App\Models\Flower;
use App\Models\Fruit;
use App\Models\Harvest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DurianSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = 'https://ugigkldnrrwsxdshpxjz.supabase.co/storage/v1/object/public/kalangka';
        $treeImageUrl = "{$baseUrl}/duriantree.jpg";

        $trees = [
            // D1 to D5 - Near J1-J5 area (North cluster)
            ['id' => 'd1e2f3a4-0001-4000-a000-000000000001', 'description' => 'D1', 'latitude' => 10.7033500, 'longitude' => 124.8031500, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0002-4000-a000-000000000002', 'description' => 'D2', 'latitude' => 10.7033800, 'longitude' => 124.8032000, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0003-4000-a000-000000000003', 'description' => 'D3', 'latitude' => 10.7026500, 'longitude' => 124.8028500, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0004-4000-a000-000000000004', 'description' => 'D4', 'latitude' => 10.7032500, 'longitude' => 124.8035000, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0005-4000-a000-000000000005', 'description' => 'D5', 'latitude' => 10.7034200, 'longitude' => 124.8033500, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],

            // D6 to D10 - Near J6-J16 area (Central cluster)
            ['id' => 'd1e2f3a4-0006-4000-a000-000000000006', 'description' => 'D6', 'latitude' => 10.7031800, 'longitude' => 124.8035500, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0007-4000-a000-000000000007', 'description' => 'D7', 'latitude' => 10.7034800, 'longitude' => 124.8037300, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0008-4000-a000-000000000008', 'description' => 'D8', 'latitude' => 10.7034200, 'longitude' => 124.8035600, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0009-4000-a000-000000000009', 'description' => 'D9', 'latitude' => 10.7027800, 'longitude' => 124.8034200, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0010-4000-a000-000000000010', 'description' => 'D10', 'latitude' => 10.7036000, 'longitude' => 124.8040900, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],

            // D11 to D15 - Near J21-J43 area (Middle cluster)
            ['id' => 'd1e2f3a4-0011-4000-a000-000000000011', 'description' => 'D11', 'latitude' => 10.7034000, 'longitude' => 124.8034700, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0012-4000-a000-000000000012', 'description' => 'D12', 'latitude' => 10.7036100, 'longitude' => 124.8040100, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0013-4000-a000-000000000013', 'description' => 'D13', 'latitude' => 10.7033900, 'longitude' => 124.8036000, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0014-4000-a000-000000000014', 'description' => 'D14', 'latitude' => 10.7030400, 'longitude' => 124.8032900, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0015-4000-a000-000000000015', 'description' => 'D15', 'latitude' => 10.7029700, 'longitude' => 124.8032900, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],

            // D16 to D20 - Near J90-J160 area (South cluster)
            ['id' => 'd1e2f3a4-0016-4000-a000-000000000016', 'description' => 'D16', 'latitude' => 10.7023200, 'longitude' => 124.8021900, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0017-4000-a000-000000000017', 'description' => 'D17', 'latitude' => 10.7028700, 'longitude' => 124.8024700, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0018-4000-a000-000000000018', 'description' => 'D18', 'latitude' => 10.7026500, 'longitude' => 124.8026300, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0019-4000-a000-000000000019', 'description' => 'D19', 'latitude' => 10.7024500, 'longitude' => 124.8024300, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'd1e2f3a4-0020-4000-a000-000000000020', 'description' => 'D20', 'latitude' => 10.7021000, 'longitude' => 124.8020500, 'status' => 'active', 'is_synced' => true, 'type' => 'Durian', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
        ];

        // Create durian trees
        $count = 0;
        foreach ($trees as $tree) {
            Tree::create($tree);
            $count++;
        }

        $this->command->info('====================================');
        $this->command->info("🌳 {$count} Durian trees seeded successfully!");
        $this->command->info('====================================');

        // Get non-admin users
        $users = User::where('role', '!=', 'admin')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('⚠️  No non-admin users found.');
            return;
        }

        // Image URLs
        $flowerImageUrls = [
            "{$baseUrl}/1stdurianflower.jpg",
            "{$baseUrl}/2nddurianflower.jpg",
            "{$baseUrl}/3rddurianflower.jpg",
            "{$baseUrl}/4thdurianflower.jpg",
        ];

        $fruitImageUrls = [
            "{$baseUrl}/1stdurianfruit.jpg",
            "{$baseUrl}/2nddurianfruit.jpg",
            "{$baseUrl}/3rddurianfruit.jpg",
            "{$baseUrl}/4thdurianfruit.jpg",
        ];

        $wasteReasons = [
            'rotten', 'pest_infestation', 'disease',
            'animal_damage', 'weather_damage', 'overripe', 'physical_damage'
        ];

        // Random date between 120-150 days ago (ensures all are harvestable)
        $getRandomDate = function() {
            $daysAgo = rand(120, 150);
            $randomDate = Carbon::now()->subDays($daysAgo);
            $randomDate->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
            return $randomDate;
        };

        $totalFlowers = 0;
        $totalFruits = 0;
        $totalHarvests = 0;
        $currentTagId = 1;
        
        // Store harvest data for later processing
        $harvestData = [];

        // Create 1 flower and 1 fruit per tree
        $this->command->info('🌸 Creating flowers & fruits for Durian trees...');

        foreach ($trees as $index => $tree) {
            $randomUser = $users[$index % $users->count()];
            $randomDate = $getRandomDate();
            
            // Fruit quantity for durian (1-3 per fruit record)
            $fruitQuantity = rand(1, 3);

            // CREATE FLOWER (1 per tree)
            $flowerId = \Illuminate\Support\Str::uuid();
            
            Flower::create([
                'id' => $flowerId,
                'tree_id' => $tree['id'],
                'user_id' => $randomUser->id,
                'quantity' => $fruitQuantity,
                'wrapped_at' => $randomDate,
                'image_url' => $flowerImageUrls[array_rand($flowerImageUrls)],
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
            $totalFlowers++;

            // CREATE FRUIT (1 per flower)
            $fruitId = \Illuminate\Support\Str::uuid();
            $tagId = $currentTagId;
            
            Fruit::create([
                'id' => $fruitId,
                'flower_id' => $flowerId,
                'tree_id' => $tree['id'],
                'user_id' => $randomUser->id,
                'quantity' => $fruitQuantity,
                'tag_id' => $tagId,
                'bagged_at' => $randomDate,
                'image_url' => $fruitImageUrls[array_rand($fruitImageUrls)],
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
            $totalFruits++;

            $currentTagId++;
            if ($currentTagId > 4) $currentTagId = 1;

            // ============ ALL HARVESTS ARE HARVESTED ============
            // Lahat ng fruits ay may harvest date at complete child records
            $hasHarvestDate = true;
            $initialStatus = 'harvested';
            
            // Harvest date is between 120-140 days after bagged
            $minHarvestDate = $randomDate->copy()->addDays(120);
            $maxHarvestDate = $randomDate->copy()->addDays(140);
            if ($maxHarvestDate > Carbon::now()) $maxHarvestDate = Carbon::now();
            
            $randomTimestamp = rand($minHarvestDate->timestamp, $maxHarvestDate->timestamp);
            $harvestDate = Carbon::createFromTimestamp($randomTimestamp);
            $harvestDate->setTime(rand(0, 23), rand(0, 59), rand(0, 59));

            $harvestId = \Illuminate\Support\Str::uuid();
            
            // Store harvest data for later processing (weights and wastes)
            $harvestData[] = [
                'id' => $harvestId,
                'fruit_id' => $fruitId,
                'user_id' => $randomUser->id,
                'fruit_quantity' => $fruitQuantity,
                'has_harvest_date' => $hasHarvestDate,
                'harvest_date' => $harvestDate,
                'initial_status' => $initialStatus,
                'index' => $index,
            ];
            
            Harvest::create([
                'id' => $harvestId,
                'fruit_id' => $fruitId,
                'user_id' => $randomUser->id,
                'ripe_quantity' => $fruitQuantity,
                'status' => $initialStatus,
                'harvest_at' => $harvestDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $totalHarvests++;
            
            $this->command->info("🌳 Tree: {$tree['description']} | Fruit quantity: {$fruitQuantity} | Status: HARVESTED");
        }

        $this->command->info("🌸 {$totalFlowers} Durian flowers created!");
        $this->command->info("🍈 {$totalFruits} Durian fruits created!");
        $this->command->info("🧺 {$totalHarvests} harvest records created!");
        $this->command->info('====================================');

        // ============ CREATE CHILD RECORDS (FRUIT WEIGHTS + WASTES) ============
        // Bawat harvest ay may fruit weights records (equal to fruit quantity)
        // Fruit weights: 3kg to 10kg (300-1000 / 100)
        // Hindi bababa sa 5 harvest records ang may waste
        
        $this->command->info('⚖️  Creating fruit weights and wastes for Durian...');
        $weightCount = 0;
        $wasteCount = 0;
        
        // Determine which harvests will have waste (at least 5)
        $harvestsWithWaste = [];
        $harvestIndices = range(0, count($harvestData) - 1);
        shuffle($harvestIndices);
        
        // Select at least 5 harvests to have waste, or 30% whichever is higher
        $wasteCount_needed = max(5, (int) round(count($harvestData) * 0.3));
        $harvestsWithWaste = array_slice($harvestIndices, 0, $wasteCount_needed);
        
        $this->command->info("   🗑️  {$wasteCount_needed} harvests will have waste records (out of " . count($harvestData) . ")");
        
        foreach ($harvestData as $idx => $data) {
            $fruitQuantity = $data['fruit_quantity'];
            $harvestId = $data['id'];
            $weightDate = Carbon::parse($data['harvest_date']);
            
            // Check if this harvest should have waste
            $hasWaste = in_array($idx, $harvestsWithWaste);
            
            if ($hasWaste) {
                // May waste: 10-30% waste
                $wastePercentage = rand(10, 30);
            } else {
                // Walang waste: 0% waste
                $wastePercentage = 0;
            }
            
            $wasteCount_for_harvest = (int) round(($wastePercentage / 100) * $fruitQuantity);
            $weighedCount = $fruitQuantity - $wasteCount_for_harvest;
            
            // Create fruit_weights for weighed fruits (3kg to 10kg)
            for ($i = 0; $i < $weighedCount; $i++) {
                // Durian weight: 3kg to 10kg (300-1000) / 100
                $weight = rand(300, 1000) / 100;
                $weightStatus = $weight < 5 ? 'local' : 'national';
                
                DB::table('fruit_weights')->insert([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'harvest_id' => $harvestId,
                    'weight' => $weight,
                    'status' => $weightStatus,
                    'created_at' => $weightDate,
                    'updated_at' => $weightDate,
                ]);
                $weightCount++;
            }
            
            // Create wastes only if this harvest is selected
            for ($i = 0; $i < $wasteCount_for_harvest; $i++) {
                $reason = $wasteReasons[array_rand($wasteReasons)];
                
                DB::table('wastes')->insert([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'harvest_id' => $harvestId,
                    'waste_quantity' => 1,
                    'reason' => $reason,
                    'reported_at' => $weightDate,
                    'created_at' => $weightDate,
                    'updated_at' => $weightDate,
                ]);
                $wasteCount++;
            }
            
            // Update harvest with actual ripe_quantity
            $harvest = Harvest::find($harvestId);
            if ($harvest) {
                $harvest->update(['ripe_quantity' => $weighedCount]);
            }
            
            $this->command->info("   📊 Harvest {$idx}: {$weighedCount} weighed, {$wasteCount_for_harvest} wasted (Total: {$fruitQuantity})");
        }
        
        $this->command->info("✅ {$weightCount} fruit weight records created!");
        $this->command->info("✅ {$wasteCount} waste records created!");
        $this->command->info('====================================');

        // ============ FINAL SUMMARY ============
        $finalHarvested = Harvest::where('status', 'harvested')
            ->whereIn('fruit_id', Fruit::whereIn('tree_id', array_column($trees, 'id'))->pluck('id'))
            ->count();
        $finalPartial = Harvest::where('status', 'partial')
            ->whereIn('fruit_id', Fruit::whereIn('tree_id', array_column($trees, 'id'))->pluck('id'))
            ->count();
        $finalPending = Harvest::where('status', 'pending')
            ->whereIn('fruit_id', Fruit::whereIn('tree_id', array_column($trees, 'id'))->pluck('id'))
            ->count();
        
        $harvestsWithWasteCount = DB::table('wastes')
            ->distinct('harvest_id')
            ->count('harvest_id');
        
        // Get weight statistics
        $minWeight = DB::table('fruit_weights')->min('weight');
        $maxWeight = DB::table('fruit_weights')->max('weight');
        $avgWeight = DB::table('fruit_weights')->avg('weight');
        
        $this->command->info('');
        $this->command->info('====================================');
        $this->command->info('📊 DURIAN SEEDER FINAL SUMMARY');
        $this->command->info('====================================');
        $this->command->info("🌳 Trees: {$count}");
        $this->command->info("🌸 Flowers: {$totalFlowers} (1 per tree)");
        $this->command->info("🍈 Fruits: {$totalFruits} (1 per flower)");
        $this->command->info("📅 Fruit dates: 120-150 days ago (all harvestable)");
        $this->command->info("🧺 Harvests created: {$totalHarvests}");
        $this->command->info("   ✅ Harvested (complete): {$finalHarvested}");
        $this->command->info("   ⚠️  Partial: {$finalPartial}");
        $this->command->info("   ⏳ Pending: {$finalPending}");
        $this->command->info("⚖️  Fruit weights created: {$weightCount} (100% of harvests have weights)");
        $this->command->info("   📊 Weight range: {$minWeight}kg - {$maxWeight}kg (Avg: " . round($avgWeight, 2) . "kg)");
        $this->command->info("🗑️  Waste records created: {$wasteCount}");
        $this->command->info("📊 Harvests with waste: {$harvestsWithWasteCount} (at least 5)");
        $this->command->info("📊 Total fruits processed: " . ($weightCount + $wasteCount));
        $this->command->info('====================================');
        
        // Verification
        $this->command->info('🔍 VERIFICATION:');
        
        $hasErrors = false;
        foreach ($harvestData as $data) {
            $totalWeights = DB::table('fruit_weights')->where('harvest_id', $data['id'])->count();
            $totalWastes = DB::table('wastes')->where('harvest_id', $data['id'])->sum('waste_quantity');
            $totalChildRecords = $totalWeights + $totalWastes;
            
            if ($totalChildRecords > $data['fruit_quantity']) {
                $this->command->error("   ⚠️  Harvest {$data['id']}: {$totalChildRecords} records > {$data['fruit_quantity']} fruits");
                $hasErrors = true;
            }
        }
        
        if (!$hasErrors) {
            $this->command->info("   ✅ All harvests have correct child record counts (≤ fruit quantity)");
        }
        
        // Check if at least 5 harvests have waste
        if ($harvestsWithWasteCount >= 5) {
            $this->command->info("   ✅ At least 5 harvest records have waste (Actual: {$harvestsWithWasteCount})");
        } else {
            $this->command->warn("   ⚠️  Only {$harvestsWithWasteCount} harvests have waste (should be at least 5)");
        }
        
        // Check weight range
        if ($minWeight >= 3 && $maxWeight <= 10) {
            $this->command->info("   ✅ Fruit weights are within 3kg - 10kg range");
        } else {
            $this->command->warn("   ⚠️  Fruit weights outside 3kg-10kg range (Min: {$minWeight}kg, Max: {$maxWeight}kg)");
        }
        
        $this->command->info('====================================');
    }
}
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

class PapayaSeeder extends Seeder
{
    public function run(): void
    {
        $baseUrl = 'https://ugigkldnrrwsxdshpxjz.supabase.co/storage/v1/object/public/kalangka';
        $treeImageUrl = "{$baseUrl}/PAPAYAtree.png";

        $trees = [
            // P1 to P5 - Near J1-J5 area (North cluster)
            ['id' => 'a1b2c3d4-0001-4000-a000-000000000001', 'description' => 'P1', 'latitude' => 10.7033100, 'longitude' => 124.8031800, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0002-4000-a000-000000000002', 'description' => 'P2', 'latitude' => 10.7033300, 'longitude' => 124.8032300, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0003-4000-a000-000000000003', 'description' => 'P3', 'latitude' => 10.7027200, 'longitude' => 124.8028700, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0004-4000-a000-000000000004', 'description' => 'P4', 'latitude' => 10.7032850, 'longitude' => 124.8035400, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0005-4000-a000-000000000005', 'description' => 'P5', 'latitude' => 10.7034000, 'longitude' => 124.8033900, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],

            // P6 to P10 - Near J6-J16 area (Central cluster)
            ['id' => 'a1b2c3d4-0006-4000-a000-000000000006', 'description' => 'P6', 'latitude' => 10.7032100, 'longitude' => 124.8035900, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0007-4000-a000-000000000007', 'description' => 'P7', 'latitude' => 10.7035200, 'longitude' => 124.8037700, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0008-4000-a000-000000000008', 'description' => 'P8', 'latitude' => 10.7034600, 'longitude' => 124.8036000, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0009-4000-a000-000000000009', 'description' => 'P9', 'latitude' => 10.7028300, 'longitude' => 124.8034700, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0010-4000-a000-000000000010', 'description' => 'P10', 'latitude' => 10.7036500, 'longitude' => 124.8041300, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],

            // P11 to P15 - Near J21-J43 area (Middle cluster)
            ['id' => 'a1b2c3d4-0011-4000-a000-000000000011', 'description' => 'P11', 'latitude' => 10.7034400, 'longitude' => 124.8035100, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0012-4000-a000-000000000012', 'description' => 'P12', 'latitude' => 10.7036500, 'longitude' => 124.8040500, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0013-4000-a000-000000000013', 'description' => 'P13', 'latitude' => 10.7034300, 'longitude' => 124.8036400, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0014-4000-a000-000000000014', 'description' => 'P14', 'latitude' => 10.7030800, 'longitude' => 124.8033300, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0015-4000-a000-000000000015', 'description' => 'P15', 'latitude' => 10.7030100, 'longitude' => 124.8033300, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],

            // P16 to P20 - Near J90-J160 area (South cluster)
            ['id' => 'a1b2c3d4-0016-4000-a000-000000000016', 'description' => 'P16', 'latitude' => 10.7023600, 'longitude' => 124.8022300, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0017-4000-a000-000000000017', 'description' => 'P17', 'latitude' => 10.7029100, 'longitude' => 124.8025100, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0018-4000-a000-000000000018', 'description' => 'P18', 'latitude' => 10.7026900, 'longitude' => 124.8026700, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0019-4000-a000-000000000019', 'description' => 'P19', 'latitude' => 10.7024900, 'longitude' => 124.8024700, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
            ['id' => 'a1b2c3d4-0020-4000-a000-000000000020', 'description' => 'P20', 'latitude' => 10.7021400, 'longitude' => 124.8020900, 'status' => 'active', 'is_synced' => true, 'type' => 'Papaya', 'image_url' => $treeImageUrl, 'created_at' => '2026-05-10 08:00:00', 'updated_at' => '2026-05-10 08:00:00'],
        ];

        // Create papaya trees
        $count = 0;
        foreach ($trees as $tree) {
            Tree::create($tree);
            $count++;
        }

        $this->command->info('====================================');
        $this->command->info("🌴 {$count} Papaya trees seeded successfully!");
        $this->command->info('====================================');

        // Get non-admin users
        $users = User::where('role', '!=', 'admin')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('⚠️  No non-admin users found.');
            return;
        }

        // Image URLs
        $flowerImageUrls = [
            "{$baseUrl}/papayapics/1stpapayaflower.jpg",
            "{$baseUrl}/papayapics/2ndpapayaflower.jpg",
            "{$baseUrl}/papayapics/3rdpapayaflower.jpg",
            "{$baseUrl}/papayapics/4thpapayaflower.jpg",
        ];

        $fruitImageUrls = [
            "{$baseUrl}/papayapics/1stpapayafruit.jpg",
            "{$baseUrl}/papayapics/2ndpapayafruit.jpg",
            "{$baseUrl}/papayapics/3rdpapayafruit.jpg",
            "{$baseUrl}/papayapics/4thpapayafruit.jpg",
        ];

        $wasteReasons = [
            'rotten', 'pest_infestation', 'disease',
            'animal_damage', 'weather_damage', 'overripe', 'physical_damage'
        ];

        // Random date between 120-150 days ago (para lahat harvested)
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
        $this->command->info('🌸 Creating flowers & fruits for Papaya trees...');

        foreach ($trees as $index => $tree) {
            $randomUser = $users[$index % $users->count()];
            $randomDate = $getRandomDate();
            
            // Calculate days since bagged (based on today)
            $daysSinceBagged = $randomDate->diffInDays(Carbon::now());
            
            // Papaya has more fruits per flower (2-5)
            $fruitQuantity = rand(2, 5);

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

            // ============ LAHAT HARVESTED (120+ days) ============
            $hasHarvestDate = true;
            $initialStatus = 'harvested';
            
            // Harvest date is between 115-130 days after bagged
            $minHarvestDate = $randomDate->copy()->addDays(115);
            $maxHarvestDate = $randomDate->copy()->addDays(130);
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
                'days_since_bagged' => $daysSinceBagged,
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
            
            $this->command->info("🌳 Tree: {$tree['description']} | Days since bagged: {$daysSinceBagged} | Status: {$initialStatus}");
        }

        $this->command->info("🌸 {$totalFlowers} Papaya flowers created!");
        $this->command->info("🍈 {$totalFruits} Papaya fruits created!");
        $this->command->info("🧺 {$totalHarvests} harvest records created!");
        $this->command->info('====================================');

        // ============ CREATE CHILD RECORDS (FRUIT WEIGHTS + WASTES) ============
        // Bawat harvest ay may fruit weights (3-10 records) at wastes
        $this->command->info('⚖️  Creating fruit weights and wastes for Papaya...');
        $weightCount = 0;
        $wasteCount = 0;
        
        foreach ($harvestData as $data) {
            $fruitQuantity = $data['fruit_quantity'];
            $harvestId = $data['id'];
            $weightDate = Carbon::parse($data['harvest_date']);
            
            // Number of fruit weight records: between 3 and 10 (di bababa ng 3, di tataas ng 10)
            $numberOfWeights = rand(3, 10);
            
            // Each weight record = 1 fruit
            // Total weighed fruits cannot exceed fruit quantity
            if ($numberOfWeights > $fruitQuantity) {
                $numberOfWeights = $fruitQuantity;
            }
            
            $weighedCount = $numberOfWeights;
            $wasteCount_for_harvest = $fruitQuantity - $weighedCount;
            
            // Create fruit_weights (3-10 records)
            for ($i = 0; $i < $weighedCount; $i++) {
                $weight = rand(300, 1000) / 100;
                $weightStatus = $weight < 0.8 ? 'local' : 'national';
                
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
            
            // Create wastes (remaining fruits)
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
            
            $this->command->info("   📊 Harvest: {$weighedCount} weighed (3-10 records), {$wasteCount_for_harvest} wasted (Total: {$fruitQuantity})");
        }
        
        $this->command->info("✅ {$weightCount} fruit weight records created!");
        $this->command->info("✅ {$wasteCount} waste records created!");
        $this->command->info('====================================');

        // ============ FINAL SUMMARY ============
        $finalHarvested = Harvest::where('status', 'harvested')
            ->whereIn('fruit_id', Fruit::whereIn('tree_id', array_column($trees, 'id'))->pluck('id'))
            ->count();
        
        $this->command->info('');
        $this->command->info('====================================');
        $this->command->info('📊 PAPAYA SEEDER FINAL SUMMARY');
        $this->command->info('====================================');
        $this->command->info("🌴 Trees: {$count}");
        $this->command->info("🌸 Flowers: {$totalFlowers} (1 per tree)");
        $this->command->info("🍈 Fruits: {$totalFruits} (1 per flower)");
        $this->command->info("📅 Fruit dates: 120-150 days ago");
        $this->command->info("🧺 Harvests created: {$totalHarvests}");
        $this->command->info("   ✅ Harvested (complete): {$finalHarvested}");
        $this->command->info("⚖️  Fruit weights created: {$weightCount}");
        $this->command->info("   📊 Each harvest has 3-10 fruit weight records");
        $this->command->info("🗑️  Waste records created: {$wasteCount}");
        $this->command->info("📊 Total fruits processed: " . ($weightCount + $wasteCount));
        $this->command->info('====================================');
        $this->command->info('📅 NOTE: All harvests are HARVESTED (120+ days since bagged)');
        $this->command->info('   Each harvest has 3-10 fruit weight records');
        $this->command->info('====================================');
    }
}
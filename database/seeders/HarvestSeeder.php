<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fruit;
use App\Models\User;
use App\Models\Harvest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;  

class HarvestSeeder extends Seeder
{
    public function run(): void
    {
        $fruits = Fruit::all();
        
        if ($fruits->isEmpty()) {
            $this->command->error('⚠️  No fruits found. Please run FruitSeeder first.');
            return;
        }

        $users = User::where('role', 'user')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('⚠️  No users found. Please create some users first.');
            return;
        }

        $this->command->info('====================================');
        $this->command->info('🌾 Creating harvest records for fruits...');
        $this->command->info('====================================');

        $totalHarvests = 0;
        $harvestedFruitIds = [];

        foreach ($fruits as $fruit) {
            if (in_array($fruit->id, $harvestedFruitIds)) {
                continue;
            }

            $baggedAt = Carbon::parse($fruit->bagged_at);
            $today = Carbon::now();
            
            // Calculate actual days since bagged (based on today, not random)
            $daysSinceBagged = $baggedAt->diffInDays($today);
            
            // Only create harvest if fruit is at least 110 days old
            if ($daysSinceBagged < 110) {
                $this->command->info("⏭️  Skipping fruit ID: {$fruit->id} (only {$daysSinceBagged} days old, need 110+)");
                continue;
            }
            
            $fruitQuantity = $fruit->quantity;
            $randomUser = $users->random();
            
            // Determine harvest status based on actual days since bagged
            if ($daysSinceBagged >= 120) {
                // Harvested - complete child records
                $hasHarvestDate = true;
                $initialStatus = 'harvested';
                
                // Harvest date is between 115-120 days after bagged
                $minHarvestDate = $baggedAt->copy()->addDays(115);
                $maxHarvestDate = $baggedAt->copy()->addDays(120);
                if ($maxHarvestDate > $today) $maxHarvestDate = $today;
                
                $randomTimestamp = rand($minHarvestDate->timestamp, $maxHarvestDate->timestamp);
                $harvestDate = Carbon::createFromTimestamp($randomTimestamp);
                $harvestDate->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
                
            } elseif ($daysSinceBagged >= 115) {
                // Partial - some child records
                $hasHarvestDate = true;
                $initialStatus = 'partial';
                
                // Harvest date is between 110-115 days after bagged
                $minHarvestDate = $baggedAt->copy()->addDays(110);
                $maxHarvestDate = $baggedAt->copy()->addDays(115);
                if ($maxHarvestDate > $today) $maxHarvestDate = $today;
                
                $randomTimestamp = rand($minHarvestDate->timestamp, $maxHarvestDate->timestamp);
                $harvestDate = Carbon::createFromTimestamp($randomTimestamp);
                $harvestDate->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
                
            } else {
                // Pending - 110-114 days, no harvest date yet
                $hasHarvestDate = false;
                $initialStatus = 'pending';
                $harvestDate = null;
            }

            // Create harvest record
            $harvest = Harvest::create([
                'id' => $this->generateUUID(),
                'fruit_id' => $fruit->id,
                'user_id' => $randomUser->id,
                'ripe_quantity' => $hasHarvestDate ? $fruitQuantity : null,
                'harvest_at' => $harvestDate,
                'status' => $initialStatus,
                'created_at' => $today,
                'updated_at' => $today,
            ]);
            
            $totalHarvests++;
            $harvestedFruitIds[] = $fruit->id;
            
            $this->command->info("🌾 Harvest created for fruit ID: {$fruit->id}");
            $this->command->info("   📦 Fruit quantity: {$fruitQuantity}");
            $this->command->info("   📅 Bagged at: {$baggedAt->format('Y-m-d')}");
            $this->command->info("   📊 Days since bagged: {$daysSinceBagged}");
            $this->command->info("   📍 Status: {$initialStatus}");
            $this->command->info("   📍 Harvest date: " . ($harvestDate ? $harvestDate->format('Y-m-d') : 'NOT YET SCHEDULED'));
            
            // If harvest has a date, create child records (fruit_weights and wastes)
            if ($hasHarvestDate) {
                $this->createChildRecordsForHarvest($harvest, $fruitQuantity, $initialStatus);
            } else {
                $this->command->info("   ⏳ No child records (pending harvest)");
            }
            
            $this->command->info('');
        }
        
        $this->command->info('====================================');
        $this->command->info("📊 Total harvest records created: {$totalHarvests} out of {$fruits->count()} fruits");
        $this->command->info('====================================');
        
        // Display summary by days range
        $this->displayHarvestSummary($fruits);
        
        // Update all harvest statuses based on child records
        $this->updateAllHarvestStatuses();
        
        $this->displayFinalSummary();
    }
    
    private function displayHarvestSummary($fruits): void
    {
        $this->command->info('');
        $this->command->info('📊 FRUIT AGE DISTRIBUTION:');
        
        $pending110_114 = 0;
        $partial115_119 = 0;
        $harvested120plus = 0;
        $tooYoung = 0;
        
        $today = Carbon::now();
        
        foreach ($fruits as $fruit) {
            $baggedAt = Carbon::parse($fruit->bagged_at);
            $daysSinceBagged = $baggedAt->diffInDays($today);
            
            if ($daysSinceBagged >= 120) {
                $harvested120plus++;
            } elseif ($daysSinceBagged >= 115) {
                $partial115_119++;
            } elseif ($daysSinceBagged >= 110) {
                $pending110_114++;
            } else {
                $tooYoung++;
            }
        }
        
        $this->command->info("   🟢 120+ days (Harvested): {$harvested120plus} fruits");
        $this->command->info("   🟡 115-119 days (Partial): {$partial115_119} fruits");
        $this->command->info("   🟠 110-114 days (Pending): {$pending110_114} fruits");
        $this->command->info("   ⚪ Below 110 days (No harvest): {$tooYoung} fruits");
        $this->command->info('====================================');
    }
    
    /**
     * Create fruit_weights and wastes for a harvest
     * Total child records MUST equal fruit quantity
    */
    private function createChildRecordsForHarvest($harvest, int $fruitQuantity, string $status): void
    {
        $weightDate = $harvest->harvest_at;
        $weighedCount = 0;
        $wasteCount = 0;
        
        if ($status === 'harvested') {
            // HARVESTED: Complete - all fruits have child records
            // Random waste percentage 0-15%
            $wastePercentage = rand(0, 15);
            $wasteCount = (int) round(($wastePercentage / 100) * $fruitQuantity);
            $weighedCount = $fruitQuantity - $wasteCount;
            
            $this->command->info("   📊 HARVESTED: {$weighedCount} weighed, {$wasteCount} wasted (Total: {$fruitQuantity})");
            
        } else {
            // PARTIAL: Only 1 child record total (either 1 weight OR 1 waste)
            // Randomly decide: 50% chance for 1 weight, 50% chance for 1 waste
            $isWeight = rand(0, 1) === 1;
            
            if ($isWeight) {
                $weighedCount = 1;
                $wasteCount = 0;
                $this->command->info("   📊 PARTIAL: 1 fruit weight, 0 wasted (Incomplete)");
            } else {
                $weighedCount = 0;
                $wasteCount = 1;
                $this->command->info("   📊 PARTIAL: 0 fruit weight, 1 wasted (Incomplete)");
            }
        }
        
        // Create fruit_weights
        for ($i = 0; $i < $weighedCount; $i++) {
        
           $weight = rand(300, 1000) / 100; // 3.00kg to 10.00kg
            $weightStatus = $weight < 0.8 ? 'local' : 'national';
            
            DB::table('fruit_weights')->insert([
                'id' => $this->generateUUID(),
                'harvest_id' => $harvest->id,
                'weight' => $weight,
                'status' => $weightStatus,
                'created_at' => $weightDate,
                'updated_at' => $weightDate,
            ]);
        }
        
        // Create wastes
        $wasteReasons = [
            'rotten', 'pest_infestation', 'disease', 
            'animal_damage', 'weather_damage', 'overripe', 'physical_damage'
        ];
        
        for ($i = 0; $i < $wasteCount; $i++) {
            $reason = $wasteReasons[array_rand($wasteReasons)];
            
            DB::table('wastes')->insert([
                'id' => $this->generateUUID(),
                'harvest_id' => $harvest->id,
                'waste_quantity' => 1,
                'reason' => $reason,
                'reported_at' => $weightDate,
                'created_at' => $weightDate,
                'updated_at' => $weightDate,
            ]);
        }
        
        $this->command->info("   ✅ Created {$weighedCount} fruit weights, {$wasteCount} waste records");
        $this->command->info("   🎯 Total child records: " . ($weighedCount + $wasteCount) . " / {$fruitQuantity}");
        
        // Update ripe_quantity = weighed fruits
        $harvest->update([
            'ripe_quantity' => $weighedCount
        ]);
    }
    
    /**
     * Update harvest statuses based on child records
     */
    private function updateAllHarvestStatuses(): void
    {
        $this->command->info('');
        $this->command->info('📊 Verifying harvest statuses...');
        
        $harvestStats = [
            'pending' => 0,
            'partial' => 0,
            'harvested' => 0
        ];
        
        $harvests = Harvest::all();
        $updatedCount = 0;
        
        foreach ($harvests as $harvest) {
            $fruit = Fruit::find($harvest->fruit_id);
            if (!$fruit) continue;
            
            $fruitQuantity = $fruit->quantity;
            
            $totalWeights = DB::table('fruit_weights')->where('harvest_id', $harvest->id)->count();
            $totalWastes = DB::table('wastes')->where('harvest_id', $harvest->id)->sum('waste_quantity');
            $totalChildRecords = $totalWeights + $totalWastes;
            
            $newStatus = 'pending';
            
            if ($totalChildRecords == 0 && $harvest->harvest_at === null) {
                $newStatus = 'pending';
            } elseif ($totalChildRecords >= $fruitQuantity) {
                $newStatus = 'harvested';
            } elseif ($totalChildRecords > 0 && $totalChildRecords < $fruitQuantity) {
                $newStatus = 'partial';
            } elseif ($harvest->harvest_at !== null && $totalChildRecords == 0) {
                $newStatus = 'partial';
            } else {
                $newStatus = 'pending';
            }
            
            $harvestStats[$newStatus]++;
            
            if ($harvest->status !== $newStatus) {
                $harvest->update(['status' => $newStatus]);
                $updatedCount++;
            }
        }
        
        $this->command->info('');
        $this->command->info('📊 FINAL HARVEST STATUS SUMMARY:');
        $this->command->info("   ⏳ Pending: {$harvestStats['pending']}");
        $this->command->info("   📋 Partial: {$harvestStats['partial']}");
        $this->command->info("   ✅ Harvested: {$harvestStats['harvested']}");
        $this->command->info("   🔄 Statuses updated: {$updatedCount}");
    }
    
    private function displayFinalSummary(): void
    {
        $totalHarvests = Harvest::count();
        $totalWeights = DB::table('fruit_weights')->count();
        $totalWastes = DB::table('wastes')->sum('waste_quantity');
        
        $this->command->info('');
        $this->command->info('====================================');
        $this->command->info('🌾 FINAL SEEDER SUMMARY');
        $this->command->info('====================================');
        $this->command->info("📊 Total harvests created: {$totalHarvests}");
        $this->command->info("⚖️  Total fruit weights: {$totalWeights}");
        $this->command->info("🗑️  Total waste records: {$totalWastes}");
        $this->command->info('====================================');
    }
    
    private function generateUUID(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all fruits
        $fruits = Fruit::all();
        
        if ($fruits->isEmpty()) {
            $this->command->error('⚠️  No fruits found. Please run FruitSeeder first.');
            return;
        }

        // Get all users (workers)
        $users = User::where('role', 'user')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('⚠️  No users found. Please create some users first.');
            return;
        }

        $this->command->info('====================================');
        $this->command->info('🌾 Creating harvest records for fruits...');
        $this->command->info('📅 Date range: January 2026 to ' . Carbon::now()->format('F Y'));
        $this->command->info('====================================');

        // Helper function to get random date from January 2026 to current month
        $getRandomDate = function() {
            $startDate = Carbon::create(2026, 1, 1); // January 1, 2026
            $endDate = Carbon::now(); // Current date
            
            // If start date is in the future, use current date minus 6 months
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
        
        $totalHarvests = 0;
        $harvestStats = [
            'pending' => 0,
            'partial' => 0,
            'harvested' => 0
        ];

        // Track which fruits are harvested to avoid duplicates
        $harvestedFruitIds = [];
        
        // Track date ranges
        $harvestDates = [];

        // Create harvest records
        foreach ($fruits as $fruit) {
            // Skip if already harvested
            if (in_array($fruit->id, $harvestedFruitIds)) {
                continue;
            }

            // Get random date for this harvest
            $randomDate = $getRandomDate();
            $baggedAt = Carbon::parse($fruit->bagged_at);
            
            // Calculate days between bagged_at and random date
            $daysSinceBagged = $baggedAt->diffInDays($randomDate);
            
            // Determine if fruit should be assigned for harvest (115+ days old)
            $shouldAssign = $daysSinceBagged >= 115;
            $harvestDate = null;
            $ripeQuantity = null;
            $status = 'pending';
            
            if ($shouldAssign) {
                // Check if the random date is >= 120 days after bagged
                if ($daysSinceBagged >= 120) {
                    // This harvest has a date (partial/harvested)
                    $harvestDate = $randomDate;
                    $ripeQuantity = round($fruit->quantity * (rand(50, 90) / 100));
                    $status = 'partial';
                    $harvestStats['partial']++;
                    $harvestDates[] = $harvestDate->format('Y-m-d');
                } else {
                    // Pending harvest (no date)
                    $ripeQuantity = null;
                    $status = 'pending';
                    $harvestDate = null;
                    $harvestStats['pending']++;
                }
            }
            
            // Create harvest record if should assign
            if ($shouldAssign) {
                $randomUser = $users->random();
                $harvestId = $this->generateUUID();
                
                Harvest::create([
                    'id' => $harvestId,
                    'fruit_id' => $fruit->id,
                    'user_id' => $randomUser->id,
                    'ripe_quantity' => $ripeQuantity,
                    'harvest_at' => $harvestDate,
                    'status' => $status,
                    'created_at' => $harvestDate ?? $randomDate,
                    'updated_at' => $harvestDate ?? $randomDate,
                ]);
                
                $totalHarvests++;
                $harvestedFruitIds[] = $fruit->id;
                
                $this->command->info("🌾 Harvest record created for fruit ID: {$fruit->id}");
                $this->command->info("   📅 Bagged at: {$baggedAt->format('Y-m-d')}");
                $this->command->info("   📊 Days since bagged: {$daysSinceBagged}");
                $this->command->info("   🌾 Status: {$status}");
                $this->command->info("   👤 Assigned to: {$randomUser->name}");
                $this->command->info("   🍎 Fruit quantity: {$fruit->quantity}");
                
                if ($status !== 'pending' && $ripeQuantity > 0) {
                    $this->command->info("   ✅ Ripe quantity: {$ripeQuantity}");
                    $this->command->info("   📅 Harvest date: {$harvestDate->format('F j, Y')}");
                } else {
                    $this->command->info("   ⏳ Harvest not yet performed (pending)");
                    $this->command->info("   📅 Created at: {$randomDate->format('F j, Y')}");
                }
                $this->command->info('');
            }
            
            // Stop if we reached enough harvests
            if ($totalHarvests >= 50) {
                break;
            }
        }

        // Create additional completed harvests if needed (these will have harvest dates)
        if ($totalHarvests < 50) {
            $this->command->info("⚠️  Only {$totalHarvests} harvest records created. Creating additional completed harvests...");
            
            $remainingFruits = $fruits->whereNotIn('id', $harvestedFruitIds);
            
            foreach ($remainingFruits as $fruit) {
                if ($totalHarvests >= 50) break;
                
                $randomDate = $getRandomDate();
                $baggedAt = Carbon::parse($fruit->bagged_at);
                $daysSinceBagged = $baggedAt->diffInDays($randomDate);
                
                // Ensure the date is at least 120 days after bagged
                if ($daysSinceBagged < 120) {
                    // If not enough days, adjust the date
                    $randomDate = $baggedAt->copy()->addDays(rand(120, 150));
                    if ($randomDate > Carbon::now()) {
                        $randomDate = Carbon::now();
                    }
                }
                
                $randomUser = $users->random();
                
                // Create completed harvest (with date)
                $ripeQuantity = round($fruit->quantity * (rand(50, 90) / 100));
                $status = 'partial';
                
                $harvestId = $this->generateUUID();
                
                Harvest::create([
                    'id' => $harvestId,
                    'fruit_id' => $fruit->id,
                    'user_id' => $randomUser->id,
                    'ripe_quantity' => $ripeQuantity,
                    'harvest_at' => $randomDate,
                    'status' => $status,
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);
                
                $totalHarvests++;
                $harvestStats['partial']++;
                $harvestDates[] = $randomDate->format('Y-m-d');
                
                $this->command->info("➕ Additional harvest created for fruit ID: {$fruit->id}");
                $this->command->info("   📅 Harvest date: {$randomDate->format('F j, Y')}");
                $this->command->info("   📊 Days since bagged: {$daysSinceBagged}");
                $this->command->info('');
            }
        }

        // Get date range statistics
        $oldestHarvest = Harvest::whereNotNull('harvest_at')->orderBy('harvest_at', 'asc')->first();
        $newestHarvest = Harvest::whereNotNull('harvest_at')->orderBy('harvest_at', 'desc')->first();

        $this->command->info('====================================');
        $this->command->info('🌾 HARVEST SUMMARY');
        $this->command->info('====================================');
        $this->command->info('📊 Total harvest records created: ' . $totalHarvests);
        $this->command->info('📈 Harvest Status Distribution:');
        $this->command->info('   ⏳ Pending (no harvest date): ' . $harvestStats['pending']);
        $this->command->info('   📋 Partial/Completed (has harvest date): ' . $harvestStats['partial']);
        $this->command->info('');
        $this->command->info('📅 DATE RANGE STATISTICS:');
        if ($oldestHarvest && $newestHarvest) {
            $this->command->info("   🌾 Oldest harvest date: " . $oldestHarvest->harvest_at->format('F j, Y'));
            $this->command->info("   🌾 Newest harvest date: " . $newestHarvest->harvest_at->format('F j, Y'));
            $this->command->info("   📊 Timespan: " . $oldestHarvest->harvest_at->diffForHumans($newestHarvest->harvest_at, true));
        }
        $this->command->info('====================================');
        
        // Create fruit weights for harvests that have harvest dates
        $this->createFruitWeights($getRandomDate);
        
        // Create waste records for some harvests
        $this->createWasteRecords($getRandomDate);
        
        // Update harvest status based on total processed (weights + wastes)
        $this->updateHarvestStatuses();
    }
    
    /**
     * Create fruit weight records for harvests
     */
    private function createFruitWeights(callable $getRandomDate): void
    {
        $this->command->info('');
        $this->command->info('⚖️  Creating fruit weight records for harvests...');
        
        // Only get harvests that have actual harvest dates (not pending)
        $harvests = Harvest::whereNotNull('harvest_at')
            ->whereNotNull('ripe_quantity')
            ->where('ripe_quantity', '>', 0)
            ->get();
        
        $weightCount = 0;
        $weightDates = [];
        
        foreach ($harvests as $harvest) {
            // Create weight records for 70% of harvests
            if (rand(1, 100) <= 70) {
                $numberOfWeights = rand(1, min(5, $harvest->ripe_quantity));
                $weightDate = $getRandomDate();
                
                // Ensure weight date is not before harvest date
                if ($weightDate < $harvest->harvest_at) {
                    $weightDate = $harvest->harvest_at;
                }
                
                for ($i = 0; $i < $numberOfWeights; $i++) {
                    $weight = rand(50, 300) / 100;
                    $status = $weight < 8 ? 'local' : 'national';
                    
                    $weightId = $this->generateUUID();
                    
                    DB::table('fruit_weights')->insert([
                        'id' => $weightId,
                        'harvest_id' => $harvest->id,
                        'weight' => $weight,
                        'status' => $status,
                        'created_at' => $weightDate,
                        'updated_at' => $weightDate,
                    ]);
                    
                    $weightCount++;
                    $weightDates[] = $weightDate->format('Y-m-d');
                }
            }
        }
        
        $this->command->info("✅ Created {$weightCount} fruit weight records");
        
        if (!empty($weightDates)) {
            $this->command->info("   📅 Weight dates range: " . min($weightDates) . " to " . max($weightDates));
        }
    }
    
    /**
     * Create waste records for harvests using the exact waste reasons from the frontend
     */
    private function createWasteRecords(callable $getRandomDate): void
    {
        $this->command->info('');
        $this->command->info('🗑️  Creating waste records for some harvests...');
        
        // Only get harvests that have actual harvest dates
        $harvests = Harvest::whereNotNull('harvest_at')
            ->whereNotNull('ripe_quantity')
            ->where('ripe_quantity', '>', 0)
            ->get();
        
        $wasteCount = 0;
        $totalWaste = 0;
        $wasteDates = [];
        
        // EXACT waste reasons from your frontend
        $wasteReasons = [
            'rotten',
            'pest_infestation',
            'disease',
            'animal_damage',
            'weather_damage',
            'overripe',
            'physical_damage'
        ];
        
        foreach ($harvests as $harvest) {
            // Only create waste for 30% of harvests
            if (rand(1, 100) <= 30 && $harvest->ripe_quantity > 0) {
                $wasteQuantity = rand(1, min(5, $harvest->ripe_quantity));
                
                if ($wasteQuantity > 0 && $wasteQuantity <= $harvest->ripe_quantity) {
                    $wasteId = $this->generateUUID();
                    $reason = $wasteReasons[array_rand($wasteReasons)];
                    $wasteDate = $getRandomDate();
                    
                    // Ensure waste date is not before harvest date
                    if ($wasteDate < $harvest->harvest_at) {
                        $wasteDate = $harvest->harvest_at;
                    }
                    
                    DB::table('wastes')->insert([
                        'id' => $wasteId,
                        'harvest_id' => $harvest->id,
                        'waste_quantity' => $wasteQuantity,
                        'reason' => $reason,
                        'reported_at' => $wasteDate,
                        'created_at' => $wasteDate,
                        'updated_at' => $wasteDate,
                    ]);
                    
                    $wasteCount++;
                    $totalWaste += $wasteQuantity;
                    $wasteDates[] = $wasteDate->format('Y-m-d');
                    
                    $this->command->info("   🗑️  Waste record created: {$wasteQuantity} fruits - Reason: {$reason}");
                }
            }
        }
        
        $this->command->info("✅ Created {$wasteCount} waste records with total waste: {$totalWaste} fruits");
        
        if (!empty($wasteDates)) {
            $this->command->info("   📅 Waste dates range: " . min($wasteDates) . " to " . max($wasteDates));
        }
        
        $this->displayWasteStatistics();
    }
    
    /**
     * Display waste reason statistics to verify distribution
     */
    private function displayWasteStatistics(): void
    {
        $wasteStats = DB::table('wastes')
            ->select('reason', DB::raw('COUNT(*) as count'), DB::raw('SUM(waste_quantity) as total_waste'))
            ->groupBy('reason')
            ->get();
        
        if ($wasteStats->isNotEmpty()) {
            $this->command->info('');
            $this->command->info('📊 WASTE REASON DISTRIBUTION:');
            $this->command->info('----------------------------------------');
            foreach ($wasteStats as $stat) {
                $this->command->info("   • {$stat->reason}: {$stat->count} records, {$stat->total_waste} fruits wasted");
            }
            $this->command->info('----------------------------------------');
        }
    }
    
    /**
     * Update harvest statuses based on ripe_quantity, fruit_weights, and wastes
     */
    private function updateHarvestStatuses(): void
    {
        $harvestStats = [
            'harvested' => 0,
            'partial' => 0,
            'pending' => 0
        ];
        
        $this->command->info('');
        $this->command->info('📊 Updating harvest statuses based on ripe_quantity and processed fruits...');
        
        $harvests = Harvest::all();
        $updatedCount = 0;
        
        foreach ($harvests as $harvest) {
            // Skip pending harvests (no harvest date) - they remain pending
            if ($harvest->harvest_at === null) {
                $harvestStats['pending']++;
                continue;
            }
            
            // Get fruit to know total quantity
            $fruit = Fruit::find($harvest->fruit_id);
            if (!$fruit) {
                $harvestStats['pending']++;
                continue;
            }
            
            // Get total fruit weights count (how many fruits were weighed)
            $totalWeights = DB::table('fruit_weights')
                ->where('harvest_id', $harvest->id)
                ->count();
            
            // Get total waste quantity
            $totalWastes = DB::table('wastes')
                ->where('harvest_id', $harvest->id)
                ->sum('waste_quantity');
            
            // Total processed fruits = weighed fruits + wasted fruits
            $totalProcessed = $totalWeights + $totalWastes;
            $fruitQuantity = $fruit->quantity;
            
            // Determine status based on total processed vs fruit quantity
            $newStatus = 'pending';
            
            if ($totalProcessed >= $fruitQuantity && $fruitQuantity > 0) {
                // All fruits have been processed (weighed or wasted)
                $newStatus = 'harvested';
                $harvestStats['harvested']++;
            } elseif ($totalProcessed > 0 || ($harvest->ripe_quantity && $harvest->ripe_quantity > 0)) {
                // Some fruits have been processed OR there is ripe_quantity recorded
                $newStatus = 'partial';
                $harvestStats['partial']++;
            } else {
                // No processing done yet
                $newStatus = 'pending';
                $harvestStats['pending']++;
            }
            
            // Update harvest status if changed
            if ($harvest->status !== $newStatus) {
                $harvest->update(['status' => $newStatus]);
                $updatedCount++;
                
                $this->command->info("   📝 Harvest {$harvest->id}: {$harvest->status} → {$newStatus} (Processed: {$totalProcessed}/{$fruitQuantity}, Ripe: {$harvest->ripe_quantity})");
            }
        }
        
        $this->command->info('====================================');
        $this->command->info('📊 FINAL HARVEST STATUS SUMMARY');
        $this->command->info('====================================');
        $this->command->info('   ✅ Harvested (all fruits processed): ' . ($harvestStats['harvested'] ?? 0));
        $this->command->info('   📋 Partial (some fruits processed): ' . ($harvestStats['partial'] ?? 0));
        $this->command->info('   ⏳ Pending (no harvest date or no processing): ' . ($harvestStats['pending'] ?? 0));
        $this->command->info('====================================');
        $this->command->info("✅ Updated {$updatedCount} harvest statuses");
        
        // Final verification - check for any harvests with ripe_quantity but pending status
        $incorrectStatuses = Harvest::whereNotNull('ripe_quantity')
            ->where('ripe_quantity', '>', 0)
            ->where('status', 'pending')
            ->count();
        
        if ($incorrectStatuses > 0) {
            $this->command->error("⚠️  WARNING: Found {$incorrectStatuses} harvests with ripe_quantity > 0 but status is 'pending'!");
            $this->command->error("   These should be fixed manually or by running the seeder again.");
            
            // Fix them automatically
            Harvest::whereNotNull('ripe_quantity')
                ->where('ripe_quantity', '>', 0)
                ->where('status', 'pending')
                ->update(['status' => 'partial']);
            
            $this->command->info("✅ Fixed {$incorrectStatuses} harvests by changing status to 'partial'");
        }
    }
    
    /**
     * Generate UUID v4
     */
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
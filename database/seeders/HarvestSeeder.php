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
        $this->command->info('====================================');

        $today = Carbon::now();
        
        $totalHarvests = 0;
        $harvestStats = [
            'pending' => 0,
            'partial' => 0,
            'harvested' => 0
        ];

        // Track which fruits are harvested to avoid duplicates
        $harvestedFruitIds = [];

        // Create harvest records
        foreach ($fruits as $fruit) {
            // Skip if already harvested
            if (in_array($fruit->id, $harvestedFruitIds)) {
                continue;
            }

            // Calculate days since bagged
            $baggedAt = Carbon::parse($fruit->bagged_at);
            $daysSinceBagged = $baggedAt->diffInDays($today);
            
            // Determine if fruit should be assigned for harvest
            $shouldAssign = false;
            $harvestDate = null;
            $ripeQuantity = null;
            $status = 'pending';
            
            // Fruits that are 115+ days old are ready to be assigned
            if ($daysSinceBagged >= 115) {
                $shouldAssign = true;
                
                // Check if the harvest date has passed or is in the future
                if ($daysSinceBagged >= 120) {
                    // Harvest date has passed - this should have been harvested
                    $harvestDate = $baggedAt->copy()->addDays(120);
                    
                    // Check if harvest date is in the past
                    if ($harvestDate->lte($today)) {
                        // This harvest is overdue, create with partial status
                        $ripeQuantity = round($fruit->quantity * (rand(50, 90) / 100));
                        $status = 'partial';
                        $harvestStats['partial']++;
                    } else {
                        // Future harvest date - pending (no harvest date)
                        $ripeQuantity = null;
                        $status = 'pending';
                        $harvestDate = null; // IMPORTANT: No date for pending
                        $harvestStats['pending']++;
                    }
                } else {
                    // Harvest date is in the future (between 115-119 days) - pending
                    $ripeQuantity = null;
                    $status = 'pending';
                    $harvestDate = null; // IMPORTANT: No date for pending
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
                    'harvest_at' => $harvestDate, // NULL for pending, actual date for partial/harvested
                    'status' => $status,
                    'created_at' => $harvestDate ?? $today,
                    'updated_at' => $harvestDate ?? $today,
                ]);
                
                $totalHarvests++;
                $harvestedFruitIds[] = $fruit->id;
                
                $this->command->info("🌾 Harvest record created for fruit ID: {$fruit->id}");
                $this->command->info("   📅 Bagged at: {$baggedAt->format('Y-m-d')}");
                $this->command->info("   📊 Days since: {$daysSinceBagged}");
                $this->command->info("   🌾 Status: {$status}");
                $this->command->info("   👤 Assigned to: {$randomUser->name}");
                $this->command->info("   🍎 Fruit quantity: {$fruit->quantity}");
                if ($status !== 'pending' && $ripeQuantity > 0) {
                    $this->command->info("   ✅ Ripe quantity: {$ripeQuantity}");
                    $this->command->info("   📅 Harvest date: {$harvestDate->format('Y-m-d')}");
                } else {
                    $this->command->info("   ⏳ Harvest not yet performed (no harvest date)");
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
                
                $baggedAt = Carbon::parse($fruit->bagged_at);
                $randomUser = $users->random();
                
                // Create completed harvest (past date)
                $harvestDate = $baggedAt->copy()->addDays(rand(120, 130));
                $ripeQuantity = round($fruit->quantity * (rand(50, 90) / 100));
                $status = 'partial';
                
                $harvestId = $this->generateUUID();
                
                Harvest::create([
                    'id' => $harvestId,
                    'fruit_id' => $fruit->id,
                    'user_id' => $randomUser->id,
                    'ripe_quantity' => $ripeQuantity,
                    'harvest_at' => $harvestDate,
                    'status' => $status,
                    'created_at' => $harvestDate,
                    'updated_at' => $harvestDate,
                ]);
                
                $totalHarvests++;
                $harvestStats['partial']++;
                
                $this->command->info("➕ Additional harvest created for fruit ID: {$fruit->id}");
                $this->command->info("   📅 Harvest date: {$harvestDate->format('Y-m-d')}");
                $this->command->info('');
            }
        }

        $this->command->info('====================================');
        $this->command->info('🌾 HARVEST SUMMARY (BEFORE WEIGHTS & WASTES)');
        $this->command->info('====================================');
        $this->command->info('📊 Total harvest records created: ' . $totalHarvests);
        $this->command->info('📈 Harvest Status Distribution:');
        $this->command->info('   ⏳ Pending (no harvest date): ' . $harvestStats['pending']);
        $this->command->info('   📋 Partial (has harvest date): ' . $harvestStats['partial']);
        $this->command->info('   ✅ Harvested: ' . $harvestStats['harvested']);
        $this->command->info('====================================');
        
        // Create fruit weights for harvests that have harvest dates
        $this->createFruitWeights();
        
        // Create waste records for some harvests
        $this->createWasteRecords();
        
        // Update harvest status based on total processed (weights + wastes)
        $this->updateHarvestStatuses();
    }
    
    /**
     * Create fruit weight records for harvests
     */
    private function createFruitWeights(): void
    {
        $this->command->info('');
        $this->command->info('⚖️  Creating fruit weight records for harvests...');
        
        // Only get harvests that have actual harvest dates (not pending)
        $harvests = Harvest::whereNotNull('harvest_at')
            ->whereNotNull('ripe_quantity')
            ->where('ripe_quantity', '>', 0)
            ->get();
        
        $weightCount = 0;
        
        foreach ($harvests as $harvest) {
            // Create weight records for 70% of harvests
            if (rand(1, 100) <= 70) {
                $numberOfWeights = rand(1, min(5, $harvest->ripe_quantity));
                
                for ($i = 0; $i < $numberOfWeights; $i++) {
                    $weight = rand(50, 300) / 100;
                    $status = $weight < 8 ? 'local' : 'national';
                    
                    $weightId = $this->generateUUID();
                    
                    DB::table('fruit_weights')->insert([
                        'id' => $weightId,
                        'harvest_id' => $harvest->id,
                        'weight' => $weight,
                        'status' => $status,
                        'created_at' => $harvest->harvest_at,
                        'updated_at' => $harvest->harvest_at,
                    ]);
                    
                    $weightCount++;
                }
            }
        }
        
        $this->command->info("✅ Created {$weightCount} fruit weight records");
    }
    
    /**
     * Create waste records for harvests using the exact waste reasons from the frontend
     */
    private function createWasteRecords(): void
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
                    
                    DB::table('wastes')->insert([
                        'id' => $wasteId,
                        'harvest_id' => $harvest->id,
                        'waste_quantity' => $wasteQuantity,
                        'reason' => $reason,
                        'reported_at' => $harvest->harvest_at,
                        'created_at' => $harvest->harvest_at,
                        'updated_at' => $harvest->harvest_at,
                    ]);
                    
                    $wasteCount++;
                    $totalWaste += $wasteQuantity;
                    
                    $this->command->info("   🗑️  Waste record created: {$wasteQuantity} fruits - Reason: {$reason}");
                }
            }
        }
        
        $this->command->info("✅ Created {$wasteCount} waste records with total waste: {$totalWaste} fruits");
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
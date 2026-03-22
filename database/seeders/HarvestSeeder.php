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
        $totalRipeQuantity = 0;
        $harvestStats = [
            'approaching' => 0,
            'ready' => 0,
            'overdue' => 0,
            'harvested' => 0,
            'partial' => 0,
            'pending' => 0
        ];

        // Track which fruits are harvested to avoid duplicates
        $harvestedFruitIds = [];

        // Create harvests for fruits that are ready (115+ days after bagged_at)
        foreach ($fruits as $fruit) {
            // Skip if already harvested
            if (in_array($fruit->id, $harvestedFruitIds)) {
                continue;
            }

            // Calculate days since bagged
            $baggedAt = Carbon::parse($fruit->bagged_at);
            $daysSinceBagged = $baggedAt->diffInDays($today);
            
            // Only harvest fruits that are 115+ days old
            if ($daysSinceBagged >= 115) {
                // Determine harvest date based on days since bagged
                $harvestDate = null;
                $ripeQuantity = 0;
                $status = '';
                
                if ($daysSinceBagged >= 115 && $daysSinceBagged < 120) {
                    // Approaching harvest (but not yet ready)
                    $status = 'approaching';
                    $harvestDate = $baggedAt->copy()->addDays(120);
                    $ripeQuantity = 0;
                    $harvestStats['approaching']++;
                    
                } elseif ($daysSinceBagged >= 120 && $daysSinceBagged < 125) {
                    // Ready for harvest
                    $status = 'ready';
                    $harvestDate = $baggedAt->copy()->addDays($daysSinceBagged);
                    $ripeQuantity = round($fruit->quantity * (rand(50, 90) / 100));
                    $harvestStats['ready']++;
                    
                } elseif ($daysSinceBagged >= 125) {
                    // Overdue harvest
                    $status = 'overdue';
                    $harvestDate = $baggedAt->copy()->addDays(120);
                    $ripeQuantity = round($fruit->quantity * (rand(30, 70) / 100));
                    $harvestStats['overdue']++;
                }
                
                // Only create harvest if we have ripe quantity or approaching
                if ($ripeQuantity > 0 || $status === 'approaching') {
                    // Randomly assign to a user
                    $randomUser = $users->random();
                    
                    // Generate UUID for harvest
                    $harvestId = $this->generateUUID();
                    
                    // Create harvest record
                    $harvest = Harvest::create([
                        'id' => $harvestId,
                        'fruit_id' => $fruit->id,
                        'user_id' => $randomUser->id,
                        'ripe_quantity' => $ripeQuantity > 0 ? $ripeQuantity : null,
                        'harvest_at' => $harvestDate,
                        'status' => $ripeQuantity > 0 ? 'partial' : 'pending', // Default status based on ripe quantity
                        'created_at' => $harvestDate,
                        'updated_at' => $harvestDate,
                    ]);
                    
                    $totalHarvests++;
                    $totalRipeQuantity += $ripeQuantity;
                    $harvestedFruitIds[] = $fruit->id;
                    
                    $this->command->info("🌾 Harvest created for fruit ID: {$fruit->id}");
                    $this->command->info("   📅 Bagged at: {$baggedAt->format('Y-m-d')}");
                    $this->command->info("   📊 Days since: {$daysSinceBagged}");
                    $this->command->info("   🌾 Status: {$status}");
                    $this->command->info("   👤 Assigned to: {$randomUser->name}");
                    $this->command->info("   🍎 Fruit quantity: {$fruit->quantity}");
                    $this->command->info("   ✅ Ripe quantity: {$ripeQuantity}");
                    $this->command->info("   📅 Harvest date: {$harvestDate->format('Y-m-d')}");
                    $this->command->info('');
                }
            }
            
            // Stop if we reached at least 50 harvests
            if ($totalHarvests >= 50) {
                break;
            }
        }

        // If we don't have 50 harvests, create additional ones
        if ($totalHarvests < 50) {
            $this->command->info("⚠️  Only {$totalHarvests} harvests created. Creating additional harvests...");
            
            $remainingFruits = $fruits->whereNotIn('id', $harvestedFruitIds);
            
            foreach ($remainingFruits as $fruit) {
                if ($totalHarvests >= 50) break;
                
                $baggedAt = Carbon::parse($fruit->bagged_at);
                $randomUser = $users->random();
                
                $harvestDate = $baggedAt->copy()->addDays(rand(115, 130));
                $daysSince = $baggedAt->diffInDays($harvestDate);
                
                if ($daysSince >= 115 && $daysSince < 120) {
                    $ripeQuantity = 0;
                } elseif ($daysSince >= 120 && $daysSince < 125) {
                    $ripeQuantity = round($fruit->quantity * (rand(50, 90) / 100));
                } else {
                    $ripeQuantity = round($fruit->quantity * (rand(30, 70) / 100));
                }
                
                $harvestId = $this->generateUUID();
                
                Harvest::create([
                    'id' => $harvestId,
                    'fruit_id' => $fruit->id,
                    'user_id' => $randomUser->id,
                    'ripe_quantity' => $ripeQuantity > 0 ? $ripeQuantity : null,
                    'harvest_at' => $harvestDate,
                    'status' => $ripeQuantity > 0 ? 'partial' : 'pending',
                    'created_at' => $harvestDate,
                    'updated_at' => $harvestDate,
                ]);
                
                $totalHarvests++;
                $totalRipeQuantity += $ripeQuantity;
                
                $this->command->info("➕ Additional harvest created for fruit ID: {$fruit->id}");
                $this->command->info("   📅 Harvest date: {$harvestDate->format('Y-m-d')}");
                $this->command->info('');
            }
        }

        $this->command->info('====================================');
        $this->command->info('🌾 HARVEST SUMMARY');
        $this->command->info('====================================');
        $this->command->info('📊 Total harvests created: ' . $totalHarvests);
        $this->command->info('🍎 Total ripe fruit quantity: ' . $totalRipeQuantity);
        $this->command->info('📈 Harvest Statistics:');
        $this->command->info('   🌱 Approaching: ' . $harvestStats['approaching']);
        $this->command->info('   ✅ Ready: ' . $harvestStats['ready']);
        $this->command->info('   ⚠️  Overdue: ' . $harvestStats['overdue']);
        $this->command->info('====================================');
        
        // Create fruit weights for harvests
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
        
        $harvests = Harvest::where('ripe_quantity', '>', 0)->get();
        $weightCount = 0;
        
        foreach ($harvests as $harvest) {
            // Get fruit to know total quantity
            $fruit = Fruit::find($harvest->fruit_id);
            if (!$fruit) continue;
            
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
     * Create waste records for harvests
     */
    private function createWasteRecords(): void
    {
        $this->command->info('');
        $this->command->info('🗑️  Creating waste records for some harvests...');
        
        $harvests = Harvest::where('ripe_quantity', '>', 0)->get();
        $wasteCount = 0;
        $totalWaste = 0;
        
        $wasteReasons = [
            'Overripe',
            'Insect damage',
            'Bird damage',
            'Physical damage',
            'Disease',
            'Rotting',
            'Small size',
            'Deformed shape'
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
                }
            }
        }
        
        $this->command->info("✅ Created {$wasteCount} waste records with total waste: {$totalWaste} fruits");
    }
    
    /**
     * Update harvest statuses based on total processed (fruit_weights + wastes)
     */
    private function updateHarvestStatuses(): void
    {
        $harvestStats = [
            'harvested' => 0,
            'partial' => 0,
            'pending' => 0
        ];
        $this->command->info('');
        $this->command->info('📊 Updating harvest statuses based on processed fruits...');
        
        $harvests = Harvest::all();
        $updatedCount = 0;
        
        foreach ($harvests as $harvest) {
            // Get fruit to know total quantity
            $fruit = Fruit::find($harvest->fruit_id);
            if (!$fruit) continue;
            
            // Get total fruit weights count
            $totalWeights = DB::table('fruit_weights')
                ->where('harvest_id', $harvest->id)
                ->count();
            
            // Get total waste quantity
            $totalWastes = DB::table('wastes')
                ->where('harvest_id', $harvest->id)
                ->sum('waste_quantity');
            
            // Total processed fruits = weights + wastes
            $totalProcessed = $totalWeights + $totalWastes;
            $fruitQuantity = $fruit->quantity;
            
            // Determine status based on total processed vs fruit quantity
            $newStatus = 'pending';
            if ($totalProcessed >= $fruitQuantity && $fruitQuantity > 0) {
                $newStatus = 'harvested';
                $harvestStats['harvested']++;
            } elseif ($totalProcessed > 0) {
                $newStatus = 'partial';
                $harvestStats['partial']++;
            } else {
                $newStatus = 'pending';
                $harvestStats['pending']++;
            }
            
            // Update harvest status if changed
            if ($harvest->status !== $newStatus) {
                $harvest->update(['status' => $newStatus]);
                $updatedCount++;
                
                $this->command->info("   📝 Harvest {$harvest->id}: {$harvest->status} → {$newStatus} (Processed: {$totalProcessed}/{$fruitQuantity})");
            }
        }
        
        $this->command->info('====================================');
        $this->command->info('📊 FINAL HARVEST STATUS SUMMARY');
        $this->command->info('====================================');
        $this->command->info('   ✅ Harvested: ' . ($harvestStats['harvested'] ?? 0));
        $this->command->info('   📋 Partial: ' . ($harvestStats['partial'] ?? 0));
        $this->command->info('   ⏳ Pending: ' . ($harvestStats['pending'] ?? 0));
        $this->command->info('====================================');
        $this->command->info("✅ Updated {$updatedCount} harvest statuses");
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
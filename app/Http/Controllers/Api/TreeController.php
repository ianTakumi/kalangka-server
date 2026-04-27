<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTreeRequest;
use App\Http\Requests\UpdateTreeRequest;
use App\Models\Tree;
use App\Models\Flower;     
use App\Models\Fruit;       
use App\Models\Harvest;     
use App\Models\Waste;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;   

class TreeController extends Controller
{
    /**
     * Display a listing of trees
     */
    public function index(Request $request)
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            // PostgreSQL version
            $trees = Tree::orderByRaw("CAST(REGEXP_REPLACE(description, '[^0-9]', '', 'g') AS INTEGER) ASC")->get();
        } else {
            // MySQL version (default)
            $trees = Tree::orderByRaw("CAST(REGEXP_REPLACE(description, '[^0-9]', '') AS UNSIGNED) ASC")->get();
        }
        
        return response()->json([
            'success' => true,
            'data' => $trees,
            'total' => $trees->count()
        ]);
    }
    /**
     * Store a tree with ID from React Native
     */
   public function store(Request $request)
{
    try {
        $ids = $request->ids; // Array of IDs
        $type = $request->type; // Same type for all
        
        if (!$ids || !is_array($ids)) {
            // Single tree mode (backward compatible)
            $ids = [$request->id];
            $type = $request->type;
        }
        
        $createdTrees = [];
        
        // Get the last tree to determine the starting J number
        $lastTree = Tree::orderBy('created_at', 'desc')->first();
        
        if ($lastTree && preg_match('/J(\d+)/', $lastTree->description, $matches)) {
            $startNumber = (int) $matches[1] + 1;
        } else {
            $startNumber = 1;
        }
        
        // Create multiple trees
        foreach ($ids as $index => $id) {
            $tree = Tree::create([
                'id' => $id,
                'description' => 'J' . ($startNumber + $index),
                'type' => $type,
                'status' => 'active',
                'is_synced' => true,
            ]);
            $createdTrees[] = $tree;
        }
        
        // If only 1 tree, return single object for backward compatibility
        if (count($createdTrees) === 1) {
            return response()->json([
                'success' => true,
                'message' => 'Tree created successfully',
                'data' => $createdTrees[0]
            ], 201);
        }
        
        return response()->json([
            'success' => true,
            'message' => count($createdTrees) . ' trees created successfully',
            'data' => $createdTrees
        ], 201);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create trees',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Display a specific tree
     */
    public function show($id)
    {
        $tree = Tree::find($id);
        
        if (!$tree) {
            return response()->json([
                'success' => false,
                'message' => 'Tree not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $tree // Direct model
        ]);
    }

    /**
     * Update a tree
     */
 public function update(UpdateTreeRequest $request, $id)
{
    $tree = Tree::find($id);
    
    if (!$tree) {
        return response()->json([
            'success' => false,
            'message' => 'Tree not found'
        ], 404);
    }
    
    
    $tree->update($request->validated());
    
 
    
    
    return response()->json([
        'success' => true,
        'message' => 'Tree updated successfully',
        'data' => $tree
    ]);
}
    /**
     * Delete a tree
     */
    public function destroy($id)
    {
        $tree = Tree::find($id);
        
        if (!$tree) {
            return response()->json([
                'success' => false,
                'message' => 'Tree not found'
            ], 404);
        }
        
        $tree->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tree deleted successfully'
        ]);
    }

    /**
     * Bulk sync from React Native
     */
    public function bulkSync(Request $request)
    {
        $request->validate([
            'trees' => 'required|array',
            'trees.*.id' => 'required|string|unique:trees,id',
            'trees.*.description' => 'required|string',
            'trees.*.latitude' => 'required|numeric',
            'trees.*.longitude' => 'required|numeric',
            'trees.*.type' => 'required|string',
            'trees.*.image_url' => 'required|string',
        ]);
        
        DB::beginTransaction();
        try {
            $savedTrees = [];
            
            foreach ($request->trees as $treeData) {
                $treeData['is_synced'] = true;
                $treeData['status'] = $treeData['status'] ?? 'active';
                
                $tree = Tree::create($treeData);
                $savedTrees[] = $tree;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($savedTrees) . ' trees synced successfully',
                'data' => $savedTrees // Direct array instead of TreeResource::collection
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk sync failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if tree IDs already exist
     */
    public function checkExisting(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string'
        ]);
        
        $existingIds = Tree::whereIn('id', $request->ids)
            ->pluck('id')
            ->toArray();
        
        return response()->json([
            'existing_ids' => $existingIds,
            'count' => count($existingIds)
        ]);
    }
    
    /**
     * Get tree statistics
     */
    public function statistics()
    {
        $stats = Tree::selectRaw('
            COUNT(*) as total_trees,
            COUNT(CASE WHEN status = ? THEN 1 END) as active_trees,
            COUNT(CASE WHEN status = ? THEN 1 END) as inactive_trees,
            COUNT(CASE WHEN is_synced = true THEN 1 END) as synced_trees,
            COUNT(DISTINCT type) as unique_types
        ', ['active', 'inactive'])->first();
        
        // Most common tree types
        $commonTypes = Tree::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'common_types' => $commonTypes,
                'total_by_status' => [
                    'active' => Tree::where('status', 'active')->count(),
                    'inactive' => Tree::where('status', 'inactive')->count(),
                    'removed' => Tree::where('status', 'removed')->count(),
                ]
            ]
        ]);
    }

 /**
 * Get harvest prediction for a specific tree
 */
/**
 * Get harvest prediction for a specific tree
 */
public function getHarvestPrediction($id)
{
    $tree = Tree::find($id);
    
    if (!$tree) {
        return response()->json([
            'success' => false,
            'message' => 'Tree not found'
        ], 404);
    }

    // Get current data
    $flowers = Flower::where('tree_id', $id)->get();
    $fruits = Fruit::where('tree_id', $id)->get();
    
    $historicalHarvests = Harvest::whereHas('fruit', function($q) use ($id) {
        $q->where('tree_id', $id);
    })->whereNotNull('harvest_at')->get();

    // Calculate current counts
    $totalFlowers = $flowers->sum('quantity');
    $totalFruits = $fruits->sum('quantity');
    
    // Get historical average
    $historicalAvg = $historicalHarvests->avg('ripe_quantity') ?? 0;
    
    // Get waste statistics
    $wasteStats = Waste::whereHas('harvest.fruit', function($q) use ($id) {
        $q->where('tree_id', $id);
    })->selectRaw('reason, COUNT(*) as count, SUM(waste_quantity) as total_waste')
      ->groupBy('reason')
      ->get();
    
    // PREDICTION LOGIC
    $avgFruitWeight = 10;
    $fruitSetRate = 0.35;
    
    // Check if there is ANY data
    $hasData = ($totalFruits > 0 || $totalFlowers > 0 || $historicalAvg > 0);
    
    // Calculate expected fruits based on ACTUAL data
    if ($totalFruits > 0) {
        $expectedFruits = $totalFruits;
        $confidence = 'high';
        $basis = 'current_fruits';
    } elseif ($totalFlowers > 0) {
        $expectedFruits = round($totalFlowers * $fruitSetRate);
        $confidence = 'medium';
        $basis = 'current_flowers';
    } elseif ($historicalAvg > 0) {
        $expectedFruits = round($historicalAvg);
        $confidence = 'medium';
        $basis = 'historical_data';
    } else {
        $expectedFruits = 0;
        $confidence = 'low';
        $basis = 'no_data';
    }
    
    $expectedYield = round($expectedFruits * $avgFruitWeight, 1);
    
    // ========== LOSS CALCULATION (only if has data) ==========
    if ($hasData) {
        $totalWaste = $wasteStats->sum('total_waste');
        $historicalLossPercent = $historicalAvg > 0 ? ($totalWaste / $historicalAvg) * 100 : 0;
        
        $lossBreakdown = [
            'flower_drop' => 15,
            'pest_damage' => 10,
            'disease_loss' => 5,
            'weather_loss' => 8,
            'harvest_waste' => 5
        ];
        
        if ($historicalLossPercent > 0) {
            $lossBreakdown['historical_actual'] = round($historicalLossPercent, 1);
        }
        
        $totalLoss = min(array_sum($lossBreakdown), 70);
    } else {
        // No data = no loss calculation
        $lossBreakdown = [];
        $totalLoss = 0;
    }
    
    // HARVEST WINDOW
    $harvestWindow = $this->calculateHarvestWindow($fruits);
    
    // ========== RECOMMENDATIONS (only if has data) ==========
    $recommendations = [];
    
    if ($hasData) {
        if ($totalFlowers > 0 && $totalFruits == 0) {
            $recommendations[] = 'Monitor flowers closely for fruit development';
            $recommendations[] = 'Consider applying organic fertilizer to improve fruit set';
        }
        
        if ($totalFruits > 0 && $totalFruits < 10) {
            $recommendations[] = 'Low fruit count - check for pollination issues';
            $recommendations[] = 'Remove competing flowers to focus energy on existing fruits';
        }
        
        $wasteByReason = $wasteStats->pluck('total_waste', 'reason')->toArray();
        if (isset($wasteByReason['pest_infestation']) && $wasteByReason['pest_infestation'] > 5) {
            $recommendations[] = 'High pest damage detected - increase pest monitoring';
        }
        if (isset($wasteByReason['disease']) && $wasteByReason['disease'] > 5) {
            $recommendations[] = 'Disease issues detected - consider fungicide application';
        }
        if (isset($wasteByReason['weather_damage']) && $wasteByReason['weather_damage'] > 5) {
            $recommendations[] = 'Weather damage observed - consider protective measures';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Continue regular monitoring and maintenance';
            $recommendations[] = 'Maintain proper watering and fertilization schedule';
        }
    } else {
        // No data = special message
        $recommendations[] = 'No flowers, fruits, or harvest records found.';
        $recommendations[] = 'Start adding data to enable predictions.';
    }
    
    // Get recent harvest trend
    $trend = 'stable';
    if ($historicalHarvests->count() >= 2) {
        $recentHarvests = $historicalHarvests->sortByDesc('harvest_at')->take(3);
        $lastHarvest = $recentHarvests->first()->ripe_quantity ?? 0;
        $prevHarvest = $recentHarvests->skip(1)->first()->ripe_quantity ?? 0;
        
        if ($lastHarvest > $prevHarvest * 1.1) {
            $trend = 'increasing';
        } elseif ($lastHarvest < $prevHarvest * 0.9) {
            $trend = 'decreasing';
        }
    }
    
    return response()->json([
        'success' => true,
        'data' => [
            'tree' => [
                'id' => $tree->id,
                'description' => $tree->description,
            ],
            'current_status' => [
                'total_flowers' => $totalFlowers,
                'total_fruits' => $totalFruits,
                'historical_avg_harvest' => round($historicalAvg, 1),
                'recent_trend' => $trend,
                'has_data' => $hasData  // Add this flag
            ],
            'prediction' => [
                'expected_yield_kg' => $expectedYield,
                'expected_fruits' => $expectedFruits,
                'confidence_level' => $confidence,
                'prediction_basis' => $basis,
                'loss_estimate_percent' => $totalLoss,
                'loss_breakdown' => $lossBreakdown
            ],
            'harvest_window' => $harvestWindow,
            'recommendations' => $recommendations,
            'waste_statistics' => $wasteStats
        ]
    ]);
}

/**
 * Get harvest prediction for all trees (dashboard summary)
 */
public function getAllHarvestPredictions()
{
    $trees = Tree::where('status', 'active')->get();
    $predictions = [];
    $totalPredictedYield = 0;
    
    foreach ($trees as $tree) {
        $flowers = Flower::where('tree_id', $tree->id)->sum('quantity');
        $fruits = Fruit::where('tree_id', $tree->id)->sum('quantity');
        
        // Quick prediction calculation
        if ($fruits > 0) {
            $expectedYield = $fruits * 10;
            $confidence = 'high';
            $status = 'has_fruits';
        } elseif ($flowers > 0) {
            $expectedYield = round($flowers * 0.35 * 10);
            $confidence = 'medium';
            $status = 'has_flowers';
        } else {
            $historicalAvg = Harvest::whereHas('fruit', function($q) use ($tree) {
                $q->where('tree_id', $tree->id);
            })->avg('ripe_quantity') ?? 0;
            
            if ($historicalAvg > 0) {
                $expectedYield = round($historicalAvg * 10);
                $confidence = 'medium';
            } else {
                $expectedYield = 500;
                $confidence = 'low';
            }
            $status = 'no_activity';
        }
        
        $totalPredictedYield += $expectedYield;
        
        $predictions[] = [
            'tree_id' => $tree->id,
            'tree_name' => $tree->description,
            'expected_yield_kg' => $expectedYield,
            'confidence' => $confidence,
            'status' => $status,
            'has_fruits' => $fruits > 0,
            'has_flowers' => $flowers > 0,
            'fruit_count' => $fruits,
            'flower_count' => $flowers
        ];
    }
    
    usort($predictions, function($a, $b) {
        return $b['expected_yield_kg'] <=> $a['expected_yield_kg'];
    });
    
    $topYielders = array_slice($predictions, 0, 5);
    
    return response()->json([
        'success' => true,
        'data' => [
            'total_trees' => $trees->count(),
            'total_predicted_yield_kg' => $totalPredictedYield,
            'average_yield_per_tree' => $trees->count() > 0 ? round($totalPredictedYield / $trees->count(), 1) : 0,
            'top_yielding_trees' => $topYielders,
            'predictions' => $predictions
        ]
    ]);
}

/**
 * Calculate harvest window based on existing fruits
 */
private function calculateHarvestWindow($fruits)
{
    $today = Carbon::now();
    $daysToMaturity = 150;
    
    if ($fruits->isEmpty()) {
        return [
            'earliest' => $today->addDays(60)->format('Y-m-d'),
            'expected' => $today->addDays(90)->format('Y-m-d'),
            'latest' => $today->addDays(120)->format('Y-m-d'),
            'has_fruits' => false,
            'message' => 'No fruits detected. Estimate based on flower development.'
        ];
    }
    
    $harvestDates = [];
    foreach ($fruits as $fruit) {
        if ($fruit->bagged_at) {
            $baggedDate = Carbon::parse($fruit->bagged_at);
            $harvestDates[] = $baggedDate->copy()->addDays($daysToMaturity);
        }
    }
    
    if (empty($harvestDates)) {
        return [
            'earliest' => null,
            'expected' => null,
            'latest' => null,
            'has_fruits' => true,
            'fruit_count' => $fruits->count(),
            'message' => 'Fruits detected but no bagging date recorded'
        ];
    }
    
    $earliest = min($harvestDates);
    $latest = max($harvestDates);
    $expected = $earliest->copy()->addDays($earliest->diffInDays($latest) / 2);
    
    return [
        'earliest' => $earliest->format('Y-m-d'),
        'expected' => $expected->format('Y-m-d'),
        'latest' => $latest->format('Y-m-d'),
        'has_fruits' => true,
        'fruit_count' => $fruits->count(),
        'days_until_earliest' => $today->diffInDays($earliest, false),
        'message' => count($harvestDates) . ' fruits will be ready for harvest starting ' . $earliest->format('M d, Y')
    ];
}

}
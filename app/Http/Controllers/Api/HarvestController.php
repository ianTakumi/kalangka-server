<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Harvest;
use App\Models\FruitWeight; 
use App\Models\Waste;       
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 

class HarvestController extends Controller
{
    /**
     * Display a listing of harvests.
     */
    public function index(Request $request)
    {
        $query = Harvest::with(['fruit.tree', 'fruitWeights', 'wastes']);
        
        // Filter by fruit_id if provided
        if ($request->has('fruit_id')) {
            $query->where('fruit_id', $request->fruit_id);
        }
        
        // Filter by user_id if provided
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by harvest date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('harvest_at', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        // Order by harvest date (newest first)
        $query->orderBy('harvest_at', 'desc')->orderBy('created_at', 'desc');
        
        $perPage = $request->input('per_page', 20);
        $harvests = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $harvests
        ]);
    }
    
    /**
     * Store a newly created harvest with weights and wastes.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|uuid|unique:harvests,id',
            'fruit_id' => 'required|string|exists:fruits,id',
            'ripe_quantity' => 'required|integer',
            'harvest_at' => 'required|date|before_or_equal:today',
            
            // Fruit weights validation with IDs
            'fruit_weights' => 'sometimes|array',
            'fruit_weights.*.id' => 'required|string|uuid|unique:fruit_weights,id',
            'fruit_weights.*.weight' => 'required|numeric|min:0|max:99.99',
            'fruit_weights.*.status' => 'sometimes|in:local,national',
            
            // Waste validation with IDs
            'wastes' => 'sometimes|array',
            'wastes.*.id' => 'required|string|uuid|unique:wastes,id',
            'wastes.*.waste_quantity' => 'required|integer|min:1',
            'wastes.*.reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 1. Create harvest
            $harvest = Harvest::create([
                'id' => $request->id,
                'fruit_id' => $request->fruit_id,
                'ripe_quantity' => $request->ripe_quantity,
                'harvest_at' => $request->harvest_at,
            ]);

            // 2. Create fruit weights with provided IDs
            if ($request->has('fruit_weights')) {
                foreach ($request->fruit_weights as $weightData) {
                    FruitWeight::create([
                        'id' => $weightData['id'],
                        'harvest_id' => $harvest->id,
                        'weight' => $weightData['weight'],
                        'status' => $weightData['status'] ?? 'local',
                    ]);
                }
            }

            // 3. Create wastes with provided IDs
            if ($request->has('wastes')) {
                foreach ($request->wastes as $wasteData) {
                    Waste::create([
                        'id' => $wasteData['id'],
                        'harvest_id' => $harvest->id,
                        'waste_quantity' => $wasteData['waste_quantity'],
                        'reason' => $wasteData['reason'],
                        'reported_at' => $request->harvest_at,
                    ]);
                }
            }

            DB::commit();

            // Load relationships
            $harvest->load(['fruit.tree', 'fruitWeights', 'wastes']);

            return response()->json([
                'success' => true,
                'message' => 'Harvest recorded successfully',
                'data' => $harvest
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([   
                'success' => false,
                'message' => 'Failed to save harvest',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
 * Assign harvester to a fruit (create harvest assignment)
 * This creates a harvest record with just fruit_id and user_id
 * Other fields (ripe_quantity, harvest_at) will be filled during actual harvest
 */
public function assignHarvester(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id' => 'required|string|uuid|unique:harvests,id',
        'fruit_id' => 'required|string|exists:fruits,id',
        'user_id' => 'required|string|exists:users,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();

    try {
        // Create harvest record with just fruit_id and user_id
        // ripe_quantity and harvest_at are nullable, so they can be null initially
        $harvest = Harvest::create([
            'id' => $request->id,
            'fruit_id' => $request->fruit_id,
            'user_id' => $request->user_id,
            'ripe_quantity' => 0, // Set to 0 initially, will be updated during actual harvest
            'harvest_at' => null, // Will be set during actual harvest
        ]);

        DB::commit();

        // Load relationships
        $harvest->load(['fruit', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Harvester assigned successfully',
            'data' => [
                'id' => $harvest->id,
                'fruit_id' => $harvest->fruit_id,
                'user_id' => $harvest->user_id,
                'fruit' => $harvest->fruit,
                'user' => $harvest->user,
                'status' => 'assigned',
                'created_at' => $harvest->created_at
            ]
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([   
            'success' => false,
            'message' => 'Failed to assign harvester',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Display the specified harvest.
     */
     public function show($id)
    {
        $harvest = Harvest::with(['fruit.tree', 'fruitWeights', 'wastes'])->find($id);
        
        if (!$harvest) {
            return response()->json([
                'success' => false,
                'message' => 'Harvest not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $harvest
        ]);
    }
    
/**
 * Update the specified harvest - Updates harvest, REPLACES fruit_weights and wastes if provided
 * 
 * @param Request $request
 * @param string $id
 * @return \Illuminate\Http\JsonResponse
 */
public function update(Request $request, string $id)
{
    $harvest = Harvest::find($id);
    
    if (!$harvest) {
        return response()->json([
            'success' => false,
            'message' => 'Harvest record not found'
        ], 404);
    }
    
    // Validate the request
    $validator = Validator::make($request->all(), [
        'id' => 'required|string|uuid',
        'fruit_id' => 'required|string|exists:fruits,id',
        'ripe_quantity' => 'required|integer',
        'harvest_at' => 'required|date|before_or_equal:today',
        'status' => 'required|string',
        // Fruit weights validation - HINDI required
        'fruit_weights' => 'sometimes|array',
        'fruit_weights.*.id' => 'required_with:fruit_weights|string|uuid',
        'fruit_weights.*.weight' => 'required_with:fruit_weights|numeric|min:0|max:99.99',
        'fruit_weights.*.status' => 'sometimes|in:local,national',
        
        // Waste validation - HINDI required
        'wastes' => 'sometimes|array',
        'wastes.*.id' => 'required_with:wastes|string|uuid',
        'wastes.*.waste_quantity' => 'required_with:wastes|integer|min:1',
        'wastes.*.reason' => 'required_with:wastes|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();

    try {
        // 1. UPDATE the existing harvest record
        $harvest->update([
            'fruit_id' => $request->fruit_id,
            'ripe_quantity' => $request->ripe_quantity,
            'harvest_at' => $request->harvest_at,
            'status' => $request->status,
        ]);

        // 2. REPLACE fruit_weights ONLY if provided in request
        if ($request->has('fruit_weights')) {
            // Delete all existing fruit_weights permanently
            FruitWeight::where('harvest_id', $harvest->id)->forceDelete();
            
            // Create new fruit weights from request (kahit empty array)
            foreach ($request->fruit_weights as $weightData) {
                FruitWeight::create([
                    'id' => $weightData['id'],
                    'harvest_id' => $harvest->id,
                    'weight' => $weightData['weight'],
                    'status' => $weightData['status'] ?? 
                               ($weightData['weight'] < 8 ? 'local' : 'national'),
                ]);
            }
        }

        // 3. REPLACE wastes ONLY if provided in request
        if ($request->has('wastes')) {
            // Delete all existing wastes permanently
            Waste::where('harvest_id', $harvest->id)->forceDelete();
            
            // Create new wastes from request (kahit empty array)
            foreach ($request->wastes as $wasteData) {
                Waste::create([
                    'id' => $wasteData['id'],
                    'harvest_id' => $harvest->id,
                    'waste_quantity' => $wasteData['waste_quantity'],
                    'reason' => $wasteData['reason'],
                    'reported_at' => $request->harvest_at,
                ]);
            }
        }

        DB::commit();

        // Load relationships
        $harvest->load(['fruit.tree', 'fruitWeights', 'wastes']);

        return response()->json([
            'success' => true,
            'message' => 'Harvest updated successfully',
            'data' => $harvest
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([   
            'success' => false,
            'message' => 'Failed to update harvest',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified harvest.
     */
    public function destroy(string $id)
    {
        $harvest = Harvest::find($id);
        
        if (!$harvest) {
            return response()->json([
                'success' => false,
                'message' => 'Harvest record not found'
            ], 404);
        }
        
        $harvest->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Harvest record deleted successfully'
        ]);
    }
    
    /**
     * Get harvest summary by fruit
     */
    public function summaryByFruit(Request $request)
    {
        $query = Harvest::query();
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('harvest_at', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        $summary = $query->selectRaw('
            fruit_id,
            SUM(ripe_quantity) as total_harvested,
            COUNT(*) as harvest_count,
            MIN(harvest_at) as first_harvest,
            MAX(harvest_at) as last_harvest
        ')
        ->groupBy('fruit_id')
        ->with('fruit')
        ->get();
        
        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
    
    /**
     * Get harvests by fruit ID
     */
    public function getByFruit($fruitId)
    {
        $harvests = Harvest::where('fruit_id', $fruitId)
                          ->with('fruit.tree')
                          ->orderBy('harvest_at', 'desc')
                          ->get();
        
        return response()->json([
            'success' => true,
            'data' => $harvests
        ]);
    }
    
    /**
     * Get monthly harvest summary
     */
    public function monthlySummary(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        
        $summary = Harvest::selectRaw('
            EXTRACT(MONTH FROM harvest_at) as month,
            SUM(ripe_quantity) as total_harvested,
            COUNT(*) as harvest_count
        ')
        ->whereYear('harvest_at', $year)
        ->groupByRaw('EXTRACT(MONTH FROM harvest_at)')
        ->orderBy('month')
        ->get();
        
        return response()->json([
            'success' => true,
            'year' => $year,
            'data' => $summary
        ]);
    }


     


 public function analytics_totals()
{
    $currentYear = now()->year;

    // Total harvest for the year
    $totalHarvest = Harvest::whereYear('harvest_at', $currentYear)->sum('ripe_quantity');

    // Total weight for the year
    $totalWeight = FruitWeight::whereYear('created_at', $currentYear)->sum('weight');

    // Total waste for all time (or can also filter by year if needed)
    $totalWaste = Waste::sum('waste_quantity');

    return response()->json([
        'success' => true,
        'data' => [
            'total_harvest' => $totalHarvest,
            'total_weight_kg' => round($totalWeight, 2),
            'total_waste' => $totalWaste,
        ]
    ]);
}

public function harvestSummary(Request $request)
{ 
    try {
        $start = $request->start_date;
        $end = $request->end_date;

        $harvestQuery = Harvest::query()
            ->whereNotNull('harvest_at');

        if ($start && $end) {
            $harvestQuery->whereBetween('harvest_at', [$start, $end]);
        }

        $totalHarvest = (clone $harvestQuery)->sum('ripe_quantity');

        $totalWeight = FruitWeight::whereIn(
            'harvest_id',
            (clone $harvestQuery)->pluck('id')
        )->sum('weight');

        $totalWaste = Waste::whereIn(
            'harvest_id',
            (clone $harvestQuery)->pluck('id')
        )->sum('waste_quantity');

        $totalTrees = Harvest::whereNotNull('harvest_at')
            ->join('fruits', 'harvests.fruit_id', '=', 'fruits.id')
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('harvests.harvest_at', [$start, $end]);
            })
            ->distinct('fruits.tree_id')
            ->count('fruits.tree_id');

        $avgHarvestPerTree = $totalTrees > 0 
            ? round($totalHarvest / $totalTrees, 2) 
            : 0;

        $avgWeightPerTree = $totalTrees > 0 
            ? round($totalWeight / $totalTrees, 2) 
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_harvest' => $totalHarvest,
                'total_weight' => round($totalWeight, 2),
                'total_waste' => $totalWaste,
                'total_trees' => $totalTrees,
                'avg_harvest_per_tree' => $avgHarvestPerTree,
                'avg_weight_per_tree' => $avgWeightPerTree,
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch summary',
            'error' => $e->getMessage()
        ], 500);
    }
}
 
}
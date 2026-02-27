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
        $query = Harvest::with('fruit.tree');
        
        // Filter by fruit_id if provided
        if ($request->has('fruit_id')) {
            $query->where('fruit_id', $request->fruit_id);
        }
        
        // Filter by harvest date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('harvest_at', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        // Order by harvest date (newest first)
        $query->orderBy('harvest_at', 'desc');
        
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
            'ripe_quantity' => 'required|integer|min:1',
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
     * Display the specified harvest.
     */
    public function show(string $id)
    {
        $harvest = Harvest::with('fruit.tree')->find($id);
        
        if (!$harvest) {
            return response()->json([
                'success' => false,
                'message' => 'Harvest record not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $harvest
        ]);
    }
    
    /**
     * Update the specified harvest.
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
        
        $validator = Validator::make($request->all(), [
            'fruit_id' => 'sometimes|string|exists:fruits,id',
            'ripe_quantity' => 'sometimes|integer|min:1',
            'harvest_at' => 'sometimes|date|before_or_equal:today',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $harvest->update($request->all());
        $harvest->load('fruit.tree');
        
        return response()->json([
            'success' => true,
            'message' => 'Harvest updated successfully',
            'data' => $harvest
        ]);
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
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Harvest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
            $query->whereBetween('harvest_date', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        // Order by harvest date (newest first)
        $query->orderBy('harvest_date', 'desc');
        
        $perPage = $request->input('per_page', 20);
        $harvests = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $harvests
        ]);
    }
    
    /**
     * Store a newly created harvest.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|uuid|unique:harvests,id',
            'fruit_id' => 'required|string|exists:fruits,id',
            'ripe_quantity' => 'required|integer|min:1',
            'harvest_date' => 'required|date|before_or_equal:today',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $harvest = Harvest::create($request->all());
        $harvest->load('fruit.tree');
        
        return response()->json([
            'success' => true,
            'message' => 'Harvest recorded successfully',
            'data' => $harvest
        ], 201);
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
            'harvest_date' => 'sometimes|date|before_or_equal:today',
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
            $query->whereBetween('harvest_date', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        $summary = $query->selectRaw('
            fruit_id,
            SUM(ripe_quantity) as total_harvested,
            COUNT(*) as harvest_count,
            MIN(harvest_date) as first_harvest,
            MAX(harvest_date) as last_harvest
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
                          ->orderBy('harvest_date', 'desc')
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
            EXTRACT(MONTH FROM harvest_date) as month,
            SUM(ripe_quantity) as total_harvested,
            COUNT(*) as harvest_count
        ')
        ->whereYear('harvest_date', $year)
        ->groupByRaw('EXTRACT(MONTH FROM harvest_date)')
        ->orderBy('month')
        ->get();
        
        return response()->json([
            'success' => true,
            'year' => $year,
            'data' => $summary
        ]);
    }
}
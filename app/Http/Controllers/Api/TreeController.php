<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTreeRequest;
use App\Http\Requests\UpdateTreeRequest;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TreeController extends Controller
{
    /**
     * Display a listing of trees
     */
    public function index(Request $request)
    {
        $query = Tree::query();
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Nearby trees (simple bounding box for now)
        if ($request->has(['latitude', 'longitude', 'radius'])) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = $request->radius; // in kilometers
            
            $query->whereBetween('latitude', [$lat - ($radius/111), $lat + ($radius/111)])
                  ->whereBetween('longitude', [$lng - ($radius/111), $lng + ($radius/111)]);
        }
        
        // Filter by sync status
        if ($request->has('is_synced')) {
            $query->where('is_synced', filter_var($request->is_synced, FILTER_VALIDATE_BOOLEAN));
        }
        
        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);
        
        // Pagination
        $perPage = $request->get('per_page', 20);
        $trees = $query->paginate($perPage);
        
        // Return JSON without TreeResource
        return response()->json([
            'success' => true,
            'data' => $trees->items(),
            'meta' => [
                'current_page' => $trees->currentPage(),
                'per_page' => $trees->perPage(),
                'total' => $trees->total(),
                'last_page' => $trees->lastPage(),
            ]
        ]);
    }

    /**
     * Store a tree with ID from React Native
     */
    public function store(StoreTreeRequest $request)
    {
        try {
            $tree = Tree::create($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Tree saved successfully',
                'data' => $tree // Direct model instead of TreeResource
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save tree',
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
}
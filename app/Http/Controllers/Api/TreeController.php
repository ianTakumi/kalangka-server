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
}
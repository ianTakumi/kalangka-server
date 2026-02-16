<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FruitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Fruit::query();
            
            // Optional filters
            if ($request->has('flower_id')) {
                $query->where('flower_id', $request->flower_id);
            }
            
            if ($request->has('tree_id')) {
                $query->where('tree_id', $request->tree_id);
            }
            
            // Include relationships if requested
            if ($request->has('with_flower') && $request->with_flower) {
                $query->with('flower');
            }
            
            if ($request->has('with_tree') && $request->with_tree) {
                $query->with('tree');
            }
            
            $fruits = $query->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $fruits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fruits',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|string|unique:fruits,id',
                'flower_id' => 'required|string|exists:flowers,id',
                'tree_id' => 'required|string|exists:trees,id',
                'quantity' => 'required|integer|min:1',
                'wrappted_at' => 'nullable|date',
                'image_url' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $fruit = Fruit::create([
                'id' => $request->id,
                'flower_id' => $request->flower_id,
                'tree_id' => $request->tree_id,
                'quantity' => $request->quantity,
                'wrappted_at' => $request->wrappted_at ?? now(),
                'image_url' => $request->image_url ?? '',
            ]);

            // Load relationships
            $fruit->load(['flower', 'tree']);

            return response()->json([
                'success' => true,
                'message' => 'Fruit created successfully',
                'data' => $fruit
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create fruit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Fruit $fruit)
    {
        try {
            // Load relationships
            $fruit->load(['flower', 'tree']);
            
            return response()->json([
                'success' => true,
                'data' => $fruit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fruit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fruit $fruit)
    {
        try {
            $validator = Validator::make($request->all(), [
                'flower_id' => 'sometimes|string|exists:flowers,id',
                'tree_id' => 'sometimes|string|exists:trees,id',
                'quantity' => 'sometimes|integer|min:1',
                'wrappted_at' => 'sometimes|date',
                'image_url' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update only provided fields
            if ($request->has('flower_id')) {
                $fruit->flower_id = $request->flower_id;
            }
            
            if ($request->has('tree_id')) {
                $fruit->tree_id = $request->tree_id;
            }
            
            if ($request->has('quantity')) {
                $fruit->quantity = $request->quantity;
            }
            
            if ($request->has('wrappted_at')) {
                $fruit->wrappted_at = $request->wrappted_at;
            }
            
            if ($request->has('image_url')) {
                $fruit->image_url = $request->image_url;
            }
            
            $fruit->save();
            
            // Load relationships
            $fruit->load(['flower', 'tree']);

            return response()->json([
                'success' => true,
                'message' => 'Fruit updated successfully',
                'data' => $fruit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update fruit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fruit $fruit)
    {
        try {
            // Soft delete (if you have deleted_at column)
            // $fruit->delete();
            
            // Or hard delete (permanent)
            $fruit->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Fruit deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete fruit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fruits by flower ID
     */
    public function getByFlowerId(string $flowerId)
    {
        try {
            $fruits = Fruit::where('flower_id', $flowerId)
                          ->with(['flower', 'tree'])
                          ->orderBy('created_at', 'desc')
                          ->get();
            
            return response()->json([
                'success' => true,
                'data' => $fruits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fruits for flower',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fruits by tree ID
     */
    public function getByTreeId(string $treeId)
    {
        try {
            $fruits = Fruit::where('tree_id', $treeId)
                          ->with(['flower', 'tree'])
                          ->orderBy('created_at', 'desc')
                          ->get();
            
            return response()->json([
                'success' => true,
                'data' => $fruits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fruits for tree',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync multiple fruits (for bulk operations from mobile)
     */
    public function sync(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fruits' => 'required|array',
                'fruits.*.id' => 'required|string',
                'fruits.*.flower_id' => 'required|string|exists:flowers,id',
                'fruits.*.tree_id' => 'required|string|exists:trees,id',
                'fruits.*.quantity' => 'required|integer|min:1',
                'fruits.*.wrappted_at' => 'nullable|date',
                'fruits.*.image_url' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $results = [];
            foreach ($request->fruits as $fruitData) {
                $fruit = Fruit::updateOrCreate(
                    ['id' => $fruitData['id']],
                    [
                        'flower_id' => $fruitData['flower_id'],
                        'tree_id' => $fruitData['tree_id'],
                        'quantity' => $fruitData['quantity'],
                        'wrappted_at' => $fruitData['wrappted_at'] ?? now(),
                        'image_url' => $fruitData['image_url'] ?? '',
                    ]
                );
                $results[] = $fruit;
            }

            return response()->json([
                'success' => true,
                'message' => 'Fruits synced successfully',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync fruits',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
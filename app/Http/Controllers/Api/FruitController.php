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
            // Get all fruits with their relationships
            $fruits = Fruit::with(['flower', 'tree', 'user'])->orderBy('created_at', 'desc')->get();
            
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
                'user_id' => 'required|string|exists:users,id',
                'quantity' => 'required|integer|min:1',
                'tag_id' => 'required|integer|min:1|max:4', 
                'bagged_at' => 'nullable|date',
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
                'user_id' => $request->user_id,
                'quantity' => $request->quantity,
                'tag_id' => $request->tag_id,
                'bagged_at' => $request->bagged_at ?? now(),
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
                'user_id' => 'sometimes|string|exists:users,id',
                'quantity' => 'sometimes|integer|min:1',
                'tag_id' => 'sometimes|integer|min:1|max:4',
                'bagged_at' => 'sometimes|date',
                'image_url' => 'sometimes|string',
                // Farmer assessment fields (optional)
                'farmer_extra_days' => 'sometimes|integer|min:0',
                'farmer_assessed_at' => 'sometimes|date',
                'next_check_date' => 'sometimes|date',
                'farmer_notes' => 'sometimes|string|nullable',
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

            if ($request->has('user_id')) {
                $fruit->user_id = $request->user_id;
            }
            
            if ($request->has('quantity')) {
                $fruit->quantity = $request->quantity;
            }

            if ($request->has('tag_id')) { 
                $fruit->tag_id = $request->tag_id;
            }

            if ($request->has('bagged_at')) {
                $fruit->bagged_at = $request->bagged_at;
            }
            
            if ($request->has('image_url')) {
                $fruit->image_url = $request->image_url;
            }
            
            // Update farmer assessment fields if present
            if ($request->has('farmer_extra_days')) {
                $fruit->farmer_extra_days = $request->farmer_extra_days;
            }
            
            if ($request->has('farmer_assessed_at')) {
                $fruit->farmer_assessed_at = $request->farmer_assessed_at;
            }
            
            if ($request->has('next_check_date')) {
                $fruit->next_check_date = $request->next_check_date;
            }
            
            if ($request->has('farmer_notes')) {
                $fruit->farmer_notes = $request->farmer_notes;
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
   public function destroy($id)
    {
        try {
            // Hanapin ang fruit gamit ang $id
            $fruit = Fruit::find($id);
            
            // I-check kung exist
            if (!$fruit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fruit not found'
                ], 404);
            }
            
            // Permanent delete
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
                'fruits.*.user_id' => 'required|string|exists:users,id',
                'fruits.*.tag_id' => 'required|integer|min:1|max:4',
                'fruits.*.quantity' => 'required|integer|min:1',
                'fruits.*.bagged_at' => 'nullable|date',
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
                        'user_id' => $fruitData['user_id'],
                        'quantity' => $fruitData['quantity'],
                        'tag_id' => $fruitData['tag_id'],
                        'bagged_at' => $fruitData['bagged_at'] ?? now(),
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
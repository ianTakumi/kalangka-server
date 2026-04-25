<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FruitWeight;
use App\Models\Harvest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FruitWeightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FruitWeight::with('harvest');

        // Filter by harvest_id
        if ($request->has('harvest_id')) {
            $query->where('harvest_id', $request->harvest_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by weight range
        if ($request->has('min_weight')) {
            $query->where('weight', '>=', $request->min_weight);
        }
        if ($request->has('max_weight')) {
            $query->where('weight', '<=', $request->max_weight);
        }

        // Sort
        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Get all data without pagination
        $fruitWeights = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Fruit weights retrieved successfully',
            'data' => $fruitWeights
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'harvest_id' => 'required|string|exists:harvests,id',
            'weight' => 'required|numeric|min:0|max:99.99',
            'status' => 'sometimes|string|in:local,national',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check if harvest exists and get details
        $harvest = Harvest::find($request->harvest_id);
        if (!$harvest) {
            return response()->json([
                'success' => false,
                'message' => 'Harvest not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Create fruit weight
        $fruitWeight = FruitWeight::create([
            'id' => (string) Str::uuid(),
            'harvest_id' => $request->harvest_id,
            'weight' => $request->weight,
            'status' => $request->status ?? 'local',
        ]);

        // Load relationship
        $fruitWeight->load('harvest');

        return response()->json([
            'success' => true,
            'message' => 'Fruit weight created successfully',
            'data' => $fruitWeight
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fruitWeight = FruitWeight::with('harvest')->find($id);

        if (!$fruitWeight) {
            return response()->json([
                'success' => false,
                'message' => 'Fruit weight not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fruit weight retrieved successfully',
            'data' => $fruitWeight
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $fruitWeight = FruitWeight::find($id);

        if (!$fruitWeight) {
            return response()->json([
                'success' => false,
                'message' => 'Fruit weight not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'weight' => 'sometimes|numeric|min:0|max:99.99',
            'status' => 'sometimes|string|in:local,national',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update only provided fields
        if ($request->has('weight')) {
            $fruitWeight->weight = $request->weight;
        }
        if ($request->has('status')) {
            $fruitWeight->status = $request->status;
        }

        $fruitWeight->save();

        // Refresh and load relationship
        $fruitWeight->refresh()->load('harvest');

        return response()->json([
            'success' => true,
            'message' => 'Fruit weight updated successfully',
            'data' => $fruitWeight
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fruitWeight = FruitWeight::find($id);

        if (!$fruitWeight) {
            return response()->json([
                'success' => false,
                'message' => 'Fruit weight not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Soft delete
        $fruitWeight->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fruit weight deleted successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Get fruit weights by harvest ID
     */
    public function getByHarvest(string $harvestId)
    {
        $fruitWeights = FruitWeight::with('harvest')
            ->where('harvest_id', $harvestId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Fruit weights retrieved successfully',
            'data' => $fruitWeights
        ], Response::HTTP_OK);
    }

    /**
     * Get market distribution for a harvest
     */
    public function getMarketDistribution(string $harvestId)
    {
        $fruitWeights = FruitWeight::where('harvest_id', $harvestId)->get();

        $local = $fruitWeights->where('status', 'local');
        $national = $fruitWeights->where('status', 'national');

        $distribution = [
            'local' => [
                'count' => $local->count(),
                'total_weight' => $local->sum('weight'),
                'average_weight' => $local->avg('weight') ?? 0,
            ],
            'national' => [
                'count' => $national->count(),
                'total_weight' => $national->sum('weight'),
                'average_weight' => $national->avg('weight') ?? 0,
            ],
            'total' => [
                'count' => $fruitWeights->count(),
                'total_weight' => $fruitWeights->sum('weight'),
                'average_weight' => $fruitWeights->avg('weight') ?? 0,
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Market distribution retrieved successfully',
            'data' => $distribution
        ], Response::HTTP_OK);
    }

    /**
     * Bulk store fruit weights
     */
    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'weights' => 'required|array|min:1',
            'weights.*.harvest_id' => 'required|string|exists:harvests,id',
            'weights.*.weight' => 'required|numeric|min:0|max:99.99',
            'weights.*.status' => 'sometimes|string|in:local,national',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $created = [];
        $errors = [];

        foreach ($request->weights as $index => $weightData) {
            try {
                $fruitWeight = FruitWeight::create([
                    'id' => (string) Str::uuid(),
                    'harvest_id' => $weightData['harvest_id'],
                    'weight' => $weightData['weight'],
                    'status' => $weightData['status'] ?? 'local',
                ]);
                
                $created[] = $fruitWeight->load('harvest');
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'data' => $weightData,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . ' fruit weights created successfully',
            'data' => [
                'created' => $created,
                'failed' => $errors,
                'total_created' => count($created),
                'total_failed' => count($errors)
            ]
        ], count($errors) > 0 ? Response::HTTP_PARTIAL_CONTENT : Response::HTTP_CREATED);
    }

    /**
     * Get weight statistics for a harvest
     */
    public function getStatistics(string $harvestId)
    {
        $fruitWeights = FruitWeight::where('harvest_id', $harvestId)->get();

        if ($fruitWeights->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No fruit weights found',
                'data' => [
                    'total_fruits' => 0,
                    'total_weight' => 0,
                    'average_weight' => 0,
                    'min_weight' => 0,
                    'max_weight' => 0,
                    'local_count' => 0,
                    'national_count' => 0,
                ]
            ], Response::HTTP_OK);
        }

        $statistics = [
            'total_fruits' => $fruitWeights->count(),
            'total_weight' => round($fruitWeights->sum('weight'), 2),
            'average_weight' => round($fruitWeights->avg('weight'), 2),
            'min_weight' => $fruitWeights->min('weight'),
            'max_weight' => $fruitWeights->max('weight'),
            'local_count' => $fruitWeights->where('status', 'local')->count(),
            'national_count' => $fruitWeights->where('status', 'national')->count(),
            'local_weight' => round($fruitWeights->where('status', 'local')->sum('weight'), 2),
            'national_weight' => round($fruitWeights->where('status', 'national')->sum('weight'), 2),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => $statistics
        ], Response::HTTP_OK);
    }

    /**
     * Sync fruit weights from mobile
     */
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fruit_weights' => 'required|array',
            'fruit_weights.*.id' => 'required|string',
            'fruit_weights.*.harvest_id' => 'required|string|exists:harvests,id',
            'fruit_weights.*.weight' => 'required|numeric|min:0|max:99.99',
            'fruit_weights.*.status' => 'sometimes|string|in:local,national',
            'fruit_weights.*.created_at' => 'sometimes|date',
            'fruit_weights.*.updated_at' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $synced = [];
        $errors = [];

        foreach ($request->fruit_weights as $weightData) {
            try {
                $fruitWeight = FruitWeight::updateOrCreate(
                    ['id' => $weightData['id']],
                    [
                        'harvest_id' => $weightData['harvest_id'],
                        'weight' => $weightData['weight'],
                        'status' => $weightData['status'] ?? 'local',
                        'created_at' => $weightData['created_at'] ?? now(),
                        'updated_at' => $weightData['updated_at'] ?? now(),
                    ]
                );
                
                $synced[] = $fruitWeight->load('harvest');
            } catch (\Exception $e) {
                $errors[] = [
                    'id' => $weightData['id'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($synced) . ' fruit weights synced successfully',
            'data' => [
                'synced' => $synced,
                'failed' => $errors,
                'total_synced' => count($synced),
                'total_failed' => count($errors)
            ]
        ], count($errors) > 0 ? Response::HTTP_PARTIAL_CONTENT : Response::HTTP_OK);
    }
}
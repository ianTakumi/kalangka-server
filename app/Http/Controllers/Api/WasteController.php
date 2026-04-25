<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Waste;
use App\Models\Harvest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WasteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 public function index(Request $request)
    {
        $query = Waste::with('harvest');

        // Filter by harvest_id
        if ($request->has('harvest_id')) {
            $query->where('harvest_id', $request->harvest_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('reported_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('reported_at', '<=', $request->to_date);
        }

        // Filter by reason
        if ($request->has('reason')) {
            $query->where('reason', 'LIKE', '%' . $request->reason . '%');
        }

        // Filter by minimum waste quantity
        if ($request->has('min_quantity')) {
            $query->where('waste_quantity', '>=', $request->min_quantity);
        }

        // Include soft deleted records
        if ($request->has('with_trashed') && $request->with_trashed) {
            $query->withTrashed();
        }

        // Only trashed records
        if ($request->has('only_trashed') && $request->only_trashed) {
            $query->onlyTrashed();
        }

        // Sort
        $sortField = $request->get('sort_by', 'reported_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Get all data without pagination
        $wastes = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Waste records retrieved successfully',
            'data' => $wastes
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'harvest_id' => 'required|string|exists:harvests,id',
            'waste_quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reported_at' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check if harvest exists
        $harvest = Harvest::find($request->harvest_id);
        if (!$harvest) {
            return response()->json([
                'success' => false,
                'message' => 'Harvest not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Create waste record
        $waste = Waste::create([
            'id' => (string) Str::uuid(),
            'harvest_id' => $request->harvest_id,
            'waste_quantity' => $request->waste_quantity,
            'reason' => $request->reason,
            'reported_at' => $request->reported_at ?? now(),
        ]);

        // Load relationship
        $waste->load('harvest');

        return response()->json([
            'success' => true,
            'message' => 'Waste record created successfully',
            'data' => $waste
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $waste = Waste::with('harvest')->find($id);

        if (!$waste) {
            return response()->json([
                'success' => false,
                'message' => 'Waste record not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Waste record retrieved successfully',
            'data' => $waste
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $waste = Waste::find($id);

        if (!$waste) {
            return response()->json([
                'success' => false,
                'message' => 'Waste record not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'waste_quantity' => 'sometimes|integer|min:1',
            'reason' => 'sometimes|string|max:255',
            'reported_at' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update only provided fields
        if ($request->has('waste_quantity')) {
            $waste->waste_quantity = $request->waste_quantity;
        }
        if ($request->has('reason')) {
            $waste->reason = $request->reason;
        }
        if ($request->has('reported_at')) {
            $waste->reported_at = $request->reported_at;
        }

        $waste->save();

        // Refresh and load relationship
        $waste->refresh()->load('harvest');

        return response()->json([
            'success' => true,
            'message' => 'Waste record updated successfully',
            'data' => $waste
        ], Response::HTTP_OK);
    }

    /**
     * Permanently delete a waste record (Force Delete)
     */
    public function forceDelete(string $id)
    {
        $waste = Waste::withTrashed()->find($id);

        if (!$waste) {
            return response()->json([
                'success' => false,
                'message' => 'Waste record not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Force delete (permanent)
        $waste->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Waste record permanently deleted successfully'
        ], Response::HTTP_OK);
    }



    /**
     * ============ BULK OPERATIONS ============
     */

    /**
     * Bulk create waste records
     */
    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wastes' => 'required|array|min:1',
            'wastes.*.harvest_id' => 'required|string|exists:harvests,id',
            'wastes.*.waste_quantity' => 'required|integer|min:1',
            'wastes.*.reason' => 'required|string|max:255',
            'wastes.*.reported_at' => 'sometimes|date',
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

        foreach ($request->wastes as $index => $wasteData) {
            try {
                // Check if harvest exists
                $harvest = Harvest::find($wasteData['harvest_id']);
                if (!$harvest) {
                    $errors[] = [
                        'index' => $index,
                        'data' => $wasteData,
                        'error' => 'Harvest not found'
                    ];
                    continue;
                }

                $waste = Waste::create([
                    'id' => (string) Str::uuid(),
                    'harvest_id' => $wasteData['harvest_id'],
                    'waste_quantity' => $wasteData['waste_quantity'],
                    'reason' => $wasteData['reason'],
                    'reported_at' => $wasteData['reported_at'] ?? now(),
                ]);
                
                $created[] = $waste->load('harvest');
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'data' => $wasteData,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . ' waste records created successfully',
            'data' => [
                'created' => $created,
                'failed' => $errors,
                'total_created' => count($created),
                'total_failed' => count($errors)
            ]
        ], count($errors) > 0 ? Response::HTTP_PARTIAL_CONTENT : Response::HTTP_CREATED);
    }

    /**
     * Bulk update waste records
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wastes' => 'required|array|min:1',
            'wastes.*.id' => 'required|string|exists:wastes,id',
            'wastes.*.waste_quantity' => 'sometimes|integer|min:1',
            'wastes.*.reason' => 'sometimes|string|max:255',
            'wastes.*.reported_at' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $updated = [];
        $errors = [];

        foreach ($request->wastes as $index => $wasteData) {
            try {
                $waste = Waste::find($wasteData['id']);
                
                if (!$waste) {
                    $errors[] = [
                        'index' => $index,
                        'data' => $wasteData,
                        'error' => 'Waste record not found'
                    ];
                    continue;
                }

                if (isset($wasteData['waste_quantity'])) {
                    $waste->waste_quantity = $wasteData['waste_quantity'];
                }
                if (isset($wasteData['reason'])) {
                    $waste->reason = $wasteData['reason'];
                }
                if (isset($wasteData['reported_at'])) {
                    $waste->reported_at = $wasteData['reported_at'];
                }

                $waste->save();
                
                $updated[] = $waste->fresh()->load('harvest');
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'data' => $wasteData,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($updated) . ' waste records updated successfully',
            'data' => [
                'updated' => $updated,
                'failed' => $errors,
                'total_updated' => count($updated),
                'total_failed' => count($errors)
            ]
        ], count($errors) > 0 ? Response::HTTP_PARTIAL_CONTENT : Response::HTTP_OK);
    }

    /**
     * Bulk force delete (permanent) waste records
     */
    public function bulkForceDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|string|exists:wastes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $deleted = [];
        $errors = [];

        foreach ($request->ids as $index => $id) {
            try {
                $waste = Waste::withTrashed()->find($id);
                
                if (!$waste) {
                    $errors[] = [
                        'index' => $index,
                        'id' => $id,
                        'error' => 'Waste record not found'
                    ];
                    continue;
                }

                $waste->forceDelete();
                $deleted[] = $id;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'id' => $id,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($deleted) . ' waste records permanently deleted successfully',
            'data' => [
                'deleted_ids' => $deleted,
                'failed' => $errors,
                'total_deleted' => count($deleted),
                'total_failed' => count($errors)
            ]
        ], count($errors) > 0 ? Response::HTTP_PARTIAL_CONTENT : Response::HTTP_OK);
    }

   

    /**
     * ============ ADDITIONAL METHODS ============
     */

    /**
     * Get wastes by harvest ID
     */
    public function getByHarvest(string $harvestId)
    {
        $wastes = Waste::with('harvest')
            ->where('harvest_id', $harvestId)
            ->orderBy('reported_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Waste records retrieved successfully',
            'data' => $wastes
        ], Response::HTTP_OK);
    }

    /**
     * Get waste statistics for a harvest
     */
    public function getHarvestWasteStats(string $harvestId)
    {
        $wastes = Waste::where('harvest_id', $harvestId)->get();

        if ($wastes->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No waste records found',
                'data' => [
                    'total_waste_quantity' => 0,
                    'total_waste_entries' => 0,
                    'average_waste_per_entry' => 0,
                    'most_common_reason' => null,
                    'reasons_breakdown' => []
                ]
            ], Response::HTTP_OK);
        }

        // Calculate statistics
        $totalQuantity = $wastes->sum('waste_quantity');
        $totalEntries = $wastes->count();
        
        // Group by reason
        $reasonsBreakdown = $wastes->groupBy('reason')
            ->map(function ($group) use ($totalEntries) {
                return [
                    'count' => $group->count(),
                    'total_quantity' => $group->sum('waste_quantity'),
                    'percentage' => round(($group->count() / $totalEntries) * 100, 2)
                ];
            });

        // Find most common reason
        $mostCommonReason = $wastes->groupBy('reason')
            ->sortByDesc(function ($group) {
                return $group->count();
            })->keys()->first();

        $statistics = [
            'total_waste_quantity' => $totalQuantity,
            'total_waste_entries' => $totalEntries,
            'average_waste_per_entry' => round($totalQuantity / $totalEntries, 2),
            'most_common_reason' => $mostCommonReason,
            'reasons_breakdown' => $reasonsBreakdown,
            'daily_average' => $this->calculateDailyAverage($wastes),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Waste statistics retrieved successfully',
            'data' => $statistics
        ], Response::HTTP_OK);
    }

    /**
     * Calculate daily average waste
     */
    private function calculateDailyAverage($wastes)
    {
        if ($wastes->isEmpty()) {
            return 0;
        }

        $firstDate = $wastes->min('reported_at');
        $lastDate = $wastes->max('reported_at');
        
        $daysDiff = now()->parse($firstDate)->diffInDays(now()->parse($lastDate)) + 1;
        
        return round($wastes->sum('waste_quantity') / $daysDiff, 2);
    }

    /**
     * Get waste summary by date range
     */
    public function getSummaryByDateRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'harvest_id' => 'sometimes|string|exists:harvests,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $query = Waste::whereDate('reported_at', '>=', $request->start_date)
            ->whereDate('reported_at', '<=', $request->end_date);

        if ($request->has('harvest_id')) {
            $query->where('harvest_id', $request->harvest_id);
        }

        $wastes = $query->get();

        // Group by date
        $dailyBreakdown = $wastes->groupBy(function ($waste) {
            return $waste->reported_at->format('Y-m-d');
        })->map(function ($dayWastes, $date) {
            return [
                'date' => $date,
                'total_quantity' => $dayWastes->sum('waste_quantity'),
                'total_entries' => $dayWastes->count(),
                'reasons' => $dayWastes->groupBy('reason')
                    ->map(fn($g) => $g->sum('waste_quantity'))
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Waste summary retrieved successfully',
            'data' => [
                'date_range' => [
                    'start' => $request->start_date,
                    'end' => $request->end_date,
                ],
                'total_waste' => $wastes->sum('waste_quantity'),
                'total_entries' => $wastes->count(),
                'daily_breakdown' => $dailyBreakdown,
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Sync waste records from mobile
     */
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wastes' => 'required|array',
            'wastes.*.id' => 'required|string',
            'wastes.*.harvest_id' => 'required|string|exists:harvests,id',
            'wastes.*.waste_quantity' => 'required|integer|min:1',
            'wastes.*.reason' => 'required|string|max:255',
            'wastes.*.reported_at' => 'sometimes|date',
            'wastes.*.created_at' => 'sometimes|date',
            'wastes.*.updated_at' => 'sometimes|date',
            'wastes.*.deleted_at' => 'sometimes|date|nullable',
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

        foreach ($request->wastes as $wasteData) {
            try {
                // Handle soft deletes from mobile
                if (isset($wasteData['deleted_at']) && $wasteData['deleted_at']) {
                    $waste = Waste::withTrashed()->find($wasteData['id']);
                    if ($waste) {
                        $waste->delete();
                    }
                    $synced[] = ['id' => $wasteData['id'], 'status' => 'deleted'];
                    continue;
                }

                // Create or update
                $waste = Waste::updateOrCreate(
                    ['id' => $wasteData['id']],
                    [
                        'harvest_id' => $wasteData['harvest_id'],
                        'waste_quantity' => $wasteData['waste_quantity'],
                        'reason' => $wasteData['reason'],
                        'reported_at' => $wasteData['reported_at'] ?? now(),
                        'created_at' => $wasteData['created_at'] ?? now(),
                        'updated_at' => $wasteData['updated_at'] ?? now(),
                    ]
                );
                
                $synced[] = $waste->load('harvest');
            } catch (\Exception $e) {
                $errors[] = [
                    'id' => $wasteData['id'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($synced) . ' waste records synced successfully',
            'data' => [
                'synced' => $synced,
                'failed' => $errors,
                'total_synced' => count($synced),
                'total_failed' => count($errors)
            ]
        ], count($errors) > 0 ? Response::HTTP_PARTIAL_CONTENT : Response::HTTP_OK);
    }
}
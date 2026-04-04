<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tree;
use App\Models\Flower;
use App\Models\Fruit;
use App\Models\Harvest;
use App\Models\FruitWeight;
use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;


class DashboardController extends Controller
{
    /**
     * Return overall totals for dashboard
     */
    public function totals(Request $request)
    {
        try {
            // Total counts
            $totalTrees = Tree::count();
            $totalFlowers = Flower::count();
            $totalFruits = Fruit::count();
            $totalUsers = User::count() - 1;
            
            // Only count COMPLETED harvests (harvested status)
            $completedHarvestIds = Harvest::where('status', 'harvested')->pluck('id');
            
            $totalHarvest = FruitWeight::whereIn('harvest_id', $completedHarvestIds)->count();
            $totalWeight = FruitWeight::whereIn('harvest_id', $completedHarvestIds)->sum('weight');
            $totalWaste = Waste::whereIn('harvest_id', $completedHarvestIds)->sum('waste_quantity');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_trees' => $totalTrees,
                    'total_flowers' => $totalFlowers,
                    'total_fruits' => $totalFruits,
                    'total_harvest' => $totalHarvest, 
                    'total_weight_kg' => (float) $totalWeight,
                    'total_wastes' => (float) $totalWaste,
                    'total_users' => $totalUsers
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard totals',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return top yielding trees by type with percentage
     */
    public function topYieldingTrees(Request $request)
    {
        try {
            $totalTrees = Tree::count();

            $treeCounts = Tree::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->orderByDesc('count')
                ->get();

            $topYielding = [];
            foreach ($treeCounts as $tree) {
                $percentage = $totalTrees > 0 ? round(($tree->count / $totalTrees) * 100) : 0;
                $topYielding[] = [
                    'type' => $tree->type,
                    'percentage' => $percentage . '%',
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $topYielding,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch top yielding trees',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

private function applyDateFilter($query, $filter, $column = 'h.harvest_at')
    {
        if (!$filter) return $query;

        $now = Carbon::now();

        switch ($filter) {
            case 'week':
                return $query->whereBetween($column, [
                    $now->startOfWeek()->toDateTimeString(),
                    $now->endOfWeek()->toDateTimeString()
                ]);

            case 'month':
                return $query->whereMonth($column, $now->month)
                             ->whereYear($column, $now->year);

            case 'year':
                return $query->whereYear($column, $now->year);

            default:
                return $query;
        }
    }

public function usersHarvestAnalytics(Request $request)
{
    try {
        $data = [];

        foreach (range(1, 12) as $month) {

            $monthName = Carbon::create()->month($month)->format('M');

            // ✅ TOTAL USERS (per month)
            $totalUsers = DB::table('users')
                ->whereMonth('created_at', $month)
                ->count();

            // ✅ ASSIGNED USERS (valid users only)
            $assignedUsers = DB::table('harvests')
                ->join('users', 'harvests.user_id', '=', 'users.id') // 🔥 FIX
                ->whereNull('harvests.harvest_at')
                ->whereMonth('harvests.created_at', $month)
                ->distinct('harvests.user_id')
                ->count('harvests.user_id');

            // ✅ HARVESTED USERS (valid users only)
            $harvestedUsers = DB::table('harvests')
                ->join('users', 'harvests.user_id', '=', 'users.id') // 🔥 FIX
                ->whereNotNull('harvests.harvest_at')
                ->whereMonth('harvests.harvest_at', $month)
                ->distinct('harvests.user_id')
                ->count('harvests.user_id');

            $data[] = [
                'month' => $monthName,
                'total' => $totalUsers,
                'assigned' => $assignedUsers,
                'harvested' => $harvestedUsers,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch user harvest analytics',
            'error' => $e->getMessage(),
        ], 500);
    }
}

  public function totalWeightPerTree(Request $request)
{
    try {
        $filter = $request->filter;

        $query = DB::table('fruit_weights as fw')
            ->join('harvests as h', 'fw.harvest_id', '=', 'h.id')
            ->join('fruits as f', 'h.fruit_id', '=', 'f.id')
            ->join('trees as t', 'f.tree_id', '=', 't.id')
            ->whereNotNull('h.harvest_at');

        // ✅ APPLY FILTER based on fw.created_at
        $query = $this->applyDateFilter($query, $filter, 'fw.created_at');

        $data = $query
            ->select(
                't.type as tree_type',
                DB::raw('SUM(fw.weight) as total_weight')
            )
            ->groupBy('t.type')
            ->orderByDesc('total_weight')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function totalHarvestPerTree(Request $request)
{
    try {
        $filter = $request->filter;

        $query = DB::table('harvests as h')
            ->join('fruits as f', 'h.fruit_id', '=', 'f.id')
            ->join('trees as t', 'f.tree_id', '=', 't.id')
            ->whereNotNull('h.harvest_at');

        // ✅ APPLY FILTER based on h.created_at instead of harvest_at
        $query = $this->applyDateFilter($query, $filter, 'h.created_at');

        $data = $query
            ->select(
                't.type as tree_type',
                DB::raw('SUM(h.ripe_quantity) as total_harvest')
            )
            ->groupBy('t.type')
            ->orderByDesc('total_harvest')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
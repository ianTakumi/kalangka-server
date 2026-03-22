<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tree;
use App\Models\Flower;
use App\Models\Fruit;
use App\Models\FruitWeight;
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

            // Total kg harvested
            $totalKg = FruitWeight::sum('weight');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_trees' => $totalTrees,
                    'total_flowers' => $totalFlowers,
                    'total_fruits' => $totalFruits,
                    'total_weight_kg' => (float) $totalKg,
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

public function totalWeightPerTree()
{
    try {
        $data = DB::table('fruit_weights as fw')
        ->join('harvests as h', 'fw.harvest_id', '=', 'h.id')
        ->join('fruits as f', 'h.fruit_id', '=', 'f.id')
        ->join('trees as t', 'f.tree_id', '=', 't.id')
        ->select(
            't.type as tree_type',
            DB::raw('SUM(fw.weight) as total_weight')
        )
        ->groupBy('t.type')
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
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tree;
use App\Models\Flower;
use App\Models\Fruit;
use App\Models\FruitWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
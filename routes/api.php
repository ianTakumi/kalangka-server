<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TreeController;
use App\Http\Controllers\Api\FlowerController;
use App\Http\Controllers\Api\FruitController;
use App\Http\Controllers\Api\HarvestController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WasteController;
use App\Http\Controllers\Api\FruitWeightController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ArticleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Tree API Routes\
Route::apiResource('trees', TreeController::class);

// Flower routes api
Route::apiResource("flowers", FlowerController::class);

// Fruit routes api
Route::apiResource("fruits", FruitController::class);

// Articles routes api
// Route::apiResource("articles", ArticleController::class);

// Harvest routes
Route::apiResource('harvests', HarvestController::class);
Route::get('fruits/{fruitId}/harvests', [HarvestController::class, 'getByFruit']);
Route::get('harvests/summary/by-fruit', [HarvestController::class, 'summaryByFruit']);
Route::get('harvests/summary/monthly', [HarvestController::class, 'monthlySummary']);
Route::get('/harvest-summary', [HarvestController::class, 'harvestSummary']);
Route::get('/summary/weekly-totals', [HarvestController::class, 'weeklySummaryTotals']);


// User routes
//
Route::get('/users/harvest-analytics', [DashboardController::class, 'usersHarvestAnalytics']);

// Public user routes - no authentication required
Route::apiResource('users', UserController::class);

// Additional user routes
Route::post('/users/check-email', [UserController::class, 'checkEmail']);



// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/check-email', [AuthController::class, 'checkEmail']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post("/change-password", [AuthController::class, 'changePassword']);
});

// Basic CRUD using apiResource
Route::apiResource('harvest', HarvestController::class);

// Custom routes (separate para hindi magulo)
Route::prefix('harvest')->group(function () {
    Route::post('/assign', [HarvestController::class, 'assignHarvester']);

    // Summary routes
    Route::get('/by-fruit/{fruitId}', [HarvestController::class, 'getByFruit']);
    Route::get('/summary/by-fruit', [HarvestController::class, 'summaryByFruit']);
    Route::get('/summary/monthly', [HarvestController::class, 'monthlySummary']);
});

// Waste routes
Route::apiResource('wastes', WasteController::class);

// Waste Custom routes
Route::get('wastes/by-harvest/{harvestId}', [WasteController::class, 'getByHarvest']);
Route::get('wastes/{harvestId}/stats', [WasteController::class, 'getHarvestWasteStats']);
Route::post('wastes/summary/by-date-range', [WasteController::class, 'getSummaryByDateRange']);
Route::post('wastes/bulk/store', [WasteController::class, 'bulkStore']);
Route::post('wastes/bulk/force-delete', [WasteController::class, 'bulkForceDelete']); 
Route::post('wastes/sync', [WasteController::class, 'sync']);
Route::delete('wastes/{id}/force', [WasteController::class, 'forceDelete']);

Route::post('/make-admin', [UserController::class, 'makeAdmin']);
// Fruit weight routes
Route::apiResource('fruit-weights', FruitWeightController::class);

//

//harvests
Route::get('/summary/analytics-totals', [HarvestController::class, 'analytics_totals']);
Route::get('/tree-analytics', [DashboardController::class, 'treeAnalytics']);


Route::get('/summary/totals', [DashboardController::class, 'totals']);
Route::get('/tree/top', [DashboardController::class, 'topYieldingTrees']); 
Route::get('/per-tree/weight', [DashboardController::class, 'totalWeightPerTree']); 
Route::get('/per-tree/harvest', [DashboardController::class, 'totalHarvestPerTree']);

// Contact routes
Route::apiResource('contacts', ContactController::class);
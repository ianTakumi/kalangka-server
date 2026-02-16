<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TreeController;
use App\Http\Controllers\Api\FlowerController;
use App\Http\Controllers\Api\FruitController;
use App\Http\Controllers\Api\HarvestController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

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

// Harvest routes
Route::apiResource('harvests', HarvestController::class);
Route::get('fruits/{fruitId}/harvests', [HarvestController::class, 'getByFruit']);
Route::get('harvests/summary/by-fruit', [HarvestController::class, 'summaryByFruit']);
Route::get('harvests/summary/monthly', [HarvestController::class, 'monthlySummary']);

// User routes
// Public user routes - no authentication required
Route::apiResource('users', UserController::class);

// Additional user routes
Route::post('/users/check-email', [UserController::class, 'checkEmail']);
Route::get('/users/search', [UserController::class, 'search']);

// Profile routes (if you still want them, but public)
Route::get('/profile/{id}', [UserController::class, 'show']); // Use show method instead
Route::put('/profile/{id}', [UserController::class, 'update']); 

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/check-email', [AuthController::class, 'checkEmail']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});
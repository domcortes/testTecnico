<?php

use App\Http\Controllers\DonkiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/instruments', [DonkiController::class, 'instruments']);
Route::get('/activity-ids', [DonkiController::class, 'activityIds']);

Route::get('/instrument-usage-percentage', [DonkiController::class, 'instrumentUsagePercentage']);
Route::post('/instrument-usage', [DonkiController::class, 'instrumentUsage']);
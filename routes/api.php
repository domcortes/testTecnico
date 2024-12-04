<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\InstrumentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

/**
 * @api
 */
Route::get('/instruments', [InstrumentController::class, 'getInstruments']);
Route::get('/instrument-usage', [InstrumentController::class, 'getInstrumentsUsage']);
Route::get('/activity-ids', [ActivityController::class, 'getActivityIds']);
Route::get('/instruments-usage-percentage', [InstrumentController::class, 'getInstrumentUsagePercentage']);


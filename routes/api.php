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
Route::get('/instruments', [InstrumentController::class, 'getInstruments'])->name('instruments.index');
Route::get('/instrument-usage', [InstrumentController::class, 'getInstrumentsUsage'])->name('instrument-usage.index');
Route::get('/activity-ids', [ActivityController::class, 'getActivityIds'])->name('activity-ids.index');
Route::post('/usage-by-instrument', [InstrumentController::class, 'getUsageByInstrument'])->name('usage-by-instrument.index');

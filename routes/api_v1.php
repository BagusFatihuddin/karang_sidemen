<?php

use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\GuideController;
use App\Http\Controllers\Api\V1\PromoController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\TripPackageController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', [HealthController::class, 'ping']);
Route::get('/promos', [PromoController::class, 'index']);
Route::get('/trip-packages', [TripPackageController::class, 'index']);
Route::get('/trip-packages/{id}', [TripPackageController::class, 'show']);
Route::get('/guides', [GuideController::class, 'index']);
Route::get('/review/{token}', [ReviewController::class, 'show']);
Route::post('/review/{token}', [ReviewController::class, 'store']);

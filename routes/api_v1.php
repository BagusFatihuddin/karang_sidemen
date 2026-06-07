<?php

use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\PromoController;
use App\Http\Controllers\Api\V1\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', [HealthController::class, 'ping']);
Route::get('/promos', [PromoController::class, 'index']);
Route::get('/review/{token}', [ReviewController::class, 'show']);
Route::post('/review/{token}', [ReviewController::class, 'store']);

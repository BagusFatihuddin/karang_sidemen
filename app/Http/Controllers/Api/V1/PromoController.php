<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Promo;
use Illuminate\Http\JsonResponse;

class PromoController extends Controller
{
    public function index(): JsonResponse
    {
        $promos = Promo::active()
            ->latest()
            ->get([
                'id',
                'title',
                'description',
                'image_url',
                'external_url',
                'start_date',
                'end_date',
            ]);

        return ApiResponse::success(
            $promos
        );
    }
}

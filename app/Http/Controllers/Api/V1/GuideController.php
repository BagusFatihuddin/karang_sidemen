<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Guide;
use Illuminate\Http\JsonResponse;

class GuideController extends Controller
{
    public function index(): JsonResponse
    {
        $guides = Guide::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'bio',
                'experience',
                'photo_url',
            ]);

        return ApiResponse::success(
            $guides
        );
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Review;
use App\Models\ReviewToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $reviewToken = ReviewToken::with([
            'visitor',
            'destination',
        ])
            ->where('token', $token)
            ->first();

        if (! $reviewToken) {
            return ApiResponse::error('Token tidak valid.', 422, [
                'token_valid' => false,
                'reason' => 'not_found',
            ]);
        }

        if (! $reviewToken->isUsable()) {
            if ($reviewToken->isExpired()) {
                return ApiResponse::error('Token sudah expired.', 422, [
                    'token_valid' => false,
                    'reason' => 'expired',
                ]);
            }

            if ($reviewToken->is_used) {
                return ApiResponse::error('Token sudah digunakan.', 422, [
                    'token_valid' => false,
                    'reason' => 'used',
                ]);
            }
        }

        return ApiResponse::success([
            'token_valid' => true,
            'visitor_name' => $reviewToken->visitor?->name,
            'visitor_city' => $reviewToken->visitor?->origin_city,
            'destination_name' => $reviewToken->destination?->name,
            'destination_id' => $reviewToken->destination?->id,
        ]);
    }

    public function store(Request $request, string $token): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['required', 'string', 'max:2000'],
            'reviewer_name' => ['nullable', 'string', 'max:100'],
            'reviewer_city' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validasi gagal.', 422, $validator->errors());
        }

        $reviewToken = ReviewToken::with([
            'visitor',
            'destination',
        ])
            ->where('token', $token)
            ->first();

        if (! $reviewToken) {
            return ApiResponse::error('Token tidak valid.', 422, [
                'token_valid' => false,
                'reason' => 'not_found',
            ]);
        }

        if (! $reviewToken->isUsable()) {
            if ($reviewToken->isExpired()) {
                return ApiResponse::error('Token sudah expired.', 422, [
                    'token_valid' => false,
                    'reason' => 'expired',
                ]);
            }

            if ($reviewToken->is_used) {
                return ApiResponse::error('Token sudah digunakan.', 422, [
                    'token_valid' => false,
                    'reason' => 'used',
                ]);
            }
        }

        $data = $validator->validated();

        Review::create([
            'review_token_id' => $reviewToken->id,
            'visitor_id' => $reviewToken->visitor_id,
            'destination_id' => $reviewToken->destination_id,
            'reviewer_name' => $data['reviewer_name'] ?? $reviewToken->visitor->name,
            'reviewer_city' => $data['reviewer_city'] ?? $reviewToken->visitor->origin_city,
            'rating' => $data['rating'],
            'review_text' => $data['review_text'],
            'status' => 'pending',
        ]);

        return ApiResponse::success((object) [], 'review submitted');
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Review;
use App\Models\ReviewToken;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

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
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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
        $photoUrl = null;
        $photoPublicId = null;

        if ($request->hasFile('photo')) {
            try {
                $upload = app(CloudinaryService::class)->upload(
                    $request->file('photo'),
                    'reviews'
                );

                $photoUrl = $upload['url'] ?? null;
                $photoPublicId = $upload['public_id'] ?? null;
            } catch (Throwable $e) {
                Log::warning('Review photo upload failed', [
                    'review_token_id' => $reviewToken->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        DB::transaction(function () use ($data, $reviewToken, $photoUrl, $photoPublicId) {
            Review::create([
                'review_token_id' => $reviewToken->id,
                'visitor_id' => $reviewToken->visitor_id,
                'destination_id' => $reviewToken->destination_id,
                'reviewer_name' => $data['reviewer_name'] ?? $reviewToken->visitor->name,
                'reviewer_city' => $data['reviewer_city'] ?? $reviewToken->visitor->origin_city,
                'rating' => $data['rating'],
                'review_text' => $data['review_text'],
                'photo_url' => $photoUrl,
                'photo_public_id' => $photoPublicId,
                'status' => 'pending',
            ]);

            ReviewToken::query()
                ->where('id', $reviewToken->id)
                ->update([
                    'is_used' => true,
                    'used_at' => now(),
                ]);
        });

        return ApiResponse::success((object) [], 'review submitted');
    }
}

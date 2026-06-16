<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Review;
use App\Models\ReviewToken;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ReviewController extends Controller
{
    /**
     * Get public approved reviews.
     */
    public function index(Request $request): JsonResponse
    {
        $destinationId = $request->query('destination_id');
        $rating = $request->query('rating');
        $sort = $request->query('sort', 'latest');
        $page = (int) $request->query('page', 1);
        $version = (int) Cache::get('reviews:version', 1);
        
        $cacheKey = "reviews:v{$version}:public:" . md5(json_encode([
            'destination_id' => $destinationId,
            'rating' => $rating,
            'sort' => $sort,
            'page' => $page,
        ]));

        $result = Cache::remember($cacheKey, 30 * 60, function () use ($destinationId, $rating, $sort, $page) {
            $query = Review::where('status', 'approved');

            if ($destinationId) {
                $query->where('destination_id', $destinationId);
            }

            if ($rating) {
                $query->where('rating', $rating);
            }

            if ($sort === 'highest_rating') {
                $query->orderByDesc('rating')->orderByDesc('created_at');
            } else {
                $query->orderByDesc('created_at');
            }

            $paginated = $query->paginate(15, ['*'], 'page', $page);

            $items = [];
            foreach ($paginated->items() as $review) {
                $items[] = [
                    'id' => $review->id,
                    'reviewer_name' => $review->reviewer_name,
                    'reviewer_city' => $review->reviewer_city,
                    'rating' => $review->rating,
                    'review_text' => $review->review_text,
                    'photo_url' => $review->photo_url,
                    'destination_id' => $review->destination_id,
                    'created_at' => $review->created_at->toIso8601String(),
                ];
            }

            return [
                'data' => $items,
                'pagination' => [
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'from' => $paginated->firstItem(),
                    'to' => $paginated->lastItem(),
                ],
            ];
        });

        return response()->json($result);
    }

    /**
     * Get public pinned reviews for landing page testimonials.
     */
    public function pinned(): JsonResponse
    {
        $version = (int) Cache::get('reviews:version', 1);

        $result = Cache::remember("reviews:v{$version}:pinned", 30 * 60, function () {
            $reviews = Review::with([
                'destination:id,name',
                'visitor:id,origin_city',
            ])
                ->where('status', 'approved')
                ->where('is_pinned_global', true)
                ->latest()
                ->limit(10)
                ->get([
                    'id',
                    'destination_id',
                    'visitor_id',
                    'reviewer_name',
                    'reviewer_city',
                    'review_text',
                    'rating',
                    'photo_url',
                    'created_at',
                ]);

            return $reviews->map(fn (Review $review): array => [
                'id' => $review->id,
                'reviewer_name' => $review->reviewer_name,
                'review_text' => $review->review_text,
                'rating' => $review->rating,
                'photo_url' => $review->photo_url,
                'destination' => [
                    'name' => $review->destination?->name,
                ],
                'origin_city' => $review->visitor?->origin_city ?? $review->reviewer_city,
            ])->values()->all();
        });

        return ApiResponse::success($result);
    }

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
            'photo' => [
                'nullable',
                'file',
                'max:2048',
                function (string $attribute, mixed $value, callable $fail): void {
                    if (! $value instanceof UploadedFile) {
                        $fail('Foto review tidak valid.');

                        return;
                    }

                    $extension = strtolower(
                        $value->getClientOriginalExtension()
                    );
                    $mimeType = strtolower(
                        (string) $value->getMimeType()
                    );
                    $allowedExtensions = [
                        'jpg',
                        'jpeg',
                        'png',
                        'webp',
                    ];
                    $allowedMimeTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        'image/x-webp',
                        'application/octet-stream',
                    ];

                    if (! in_array($extension, $allowedExtensions, true)) {
                        $fail('Foto harus berformat JPG, PNG, atau WEBP.');

                        return;
                    }

                    if (! in_array($mimeType, $allowedMimeTypes, true)) {
                        $fail('Tipe file foto tidak didukung.');
                    }
                },
            ],
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

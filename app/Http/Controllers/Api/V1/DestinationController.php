<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DestinationController extends Controller
{
    /**
     * Get list of active destinations.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $page = (int) $request->query('page', 1);
        $version = (int) Cache::get('destinations:version', 1);
        $cacheKey = "destinations:v{$version}:list:" . ($type ? md5($type) : 'all') . ":page:{$page}";

        $result = Cache::remember($cacheKey, 15 * 60, function () use ($type, $page, $request) {
            $query = Destination::active()
                ->with('images', 'dailyVisits')
                ->orderBy('name');

            if ($type) {
                $query->where('destination_type', $type);
            }

            $paginated = $query->paginate(15, ['*'], 'page', $page);

            return [
                'data' => json_decode(json_encode(DestinationResource::collection($paginated->items())->toArray($request)), true),
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
     * Get single destination detail.
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $version = (int) Cache::get('destinations:version', 1);

        $result = Cache::remember("destinations:v{$version}:{$id}", 10 * 60, function () use ($id, $request) {
            $destination = Destination::active()
                ->with('images', 'dailyVisits')
                ->findOrFail($id);

            return json_decode(json_encode((new DestinationResource($destination))->toArray($request)), true);
        });

        return response()->json([
            'data' => $result,
        ]);
    }
}

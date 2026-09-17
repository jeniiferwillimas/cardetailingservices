<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Service\Models\Service;
use Modules\Service\Transformers\ServiceResource;

class ServiceController extends Controller
{
    /**
     * Public: list active services, optionally filtered by type (package|addon).
     */
    public function index(Request $request): JsonResponse
    {
        $services = Service::query()
            ->where('is_active', true)
            ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
            ->orderBy('price')
            ->get();

        return response()->json([
            'data' => ServiceResource::collection($services),
        ]);
    }

    /**
     * Public: show a single active service by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $service = Service::query()
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => new ServiceResource($service),
        ]);
    }

    /**
     * Admin: create a service.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:services,slug'],
            'type' => ['required', Rule::in(['package', 'addon'])],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_min' => ['required', 'integer', 'min:1'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $service = Service::create($validated);

        return response()->json(['data' => new ServiceResource($service)], 201);
    }

    /**
     * Admin: update a service.
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('services', 'slug')->ignore($service->id)],
            'type' => ['sometimes', Rule::in(['package', 'addon'])],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'duration_min' => ['sometimes', 'integer', 'min:1'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $service->update($validated);

        return response()->json(['data' => new ServiceResource($service)]);
    }

    /**
     * Admin: delete a service.
     */
    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json(status: 204);
    }
}

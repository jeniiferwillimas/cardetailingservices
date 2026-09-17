<?php

namespace Modules\Service\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
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
        try {
            $services = Service::query()
                ->where('is_active', true)
                ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
                ->orderBy('price')
                ->get();

            $res = [
                'success' => true,
                'data' => ServiceResource::collection($services),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Public: show a single active service by slug.
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $service = Service::query()
                ->where('is_active', true)
                ->where('slug', $slug)
                ->firstOrFail();

            $res = [
                'success' => true,
                'data' => new ServiceResource($service),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Admin: list all services, including inactive ones.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $services = Service::query()
                ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
                ->orderBy('type')
                ->orderBy('price')
                ->get();

            $res = [
                'success' => true,
                'data' => ServiceResource::collection($services),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Admin: create a service.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['required', 'string', 'max:255', 'unique:services,slug'],
                'type' => ['required', Rule::in(['package', 'addon'])],
                'description' => ['nullable', 'string'],
                'price' => ['required', 'numeric', 'min:0'],
                'duration_min' => ['required', 'integer', 'min:1'],
                'image_url' => ['nullable', 'string', 'max:2048'],
                'features' => ['nullable', 'array'],
                'features.*' => ['string'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $service = Service::create($validated);

            $res = [
                'success' => true,
                'data' => new ServiceResource($service),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Admin: update a service.
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'slug' => ['sometimes', 'string', 'max:255', Rule::unique('services', 'slug')->ignore($service->id)],
                'type' => ['sometimes', Rule::in(['package', 'addon'])],
                'description' => ['nullable', 'string'],
                'price' => ['sometimes', 'numeric', 'min:0'],
                'duration_min' => ['sometimes', 'integer', 'min:1'],
                'image_url' => ['nullable', 'string', 'max:2048'],
                'features' => ['nullable', 'array'],
                'features.*' => ['string'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $service->update($validated);

            $res = [
                'success' => true,
                'data' => new ServiceResource($service),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Admin: delete a service.
     */
    public function destroy(Service $service): JsonResponse
    {
        try {
            $service->delete();

            $res = [
                'success' => true,
                'data' => null,
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }
}

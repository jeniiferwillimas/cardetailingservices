<?php

namespace Modules\Content\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Content\Models\GalleryImage;
use Modules\Content\Transformers\GalleryImageResource;

class GalleryImageController extends Controller
{
    /**
     * Public: list active gallery images, ordered for display.
     */
    public function index(): JsonResponse
    {
        try {
            $images = GalleryImage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $res = [
                'success' => true,
                'data' => GalleryImageResource::collection($images),
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
     * Admin: add a gallery image.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'image_url' => ['required', 'string', 'max:2048'],
                'alt_text' => ['nullable', 'string', 'max:255'],
                'sort_order' => ['sometimes', 'integer'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $image = GalleryImage::create($validated);

            $res = [
                'success' => true,
                'data' => new GalleryImageResource($image),
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
     * Admin: update a gallery image.
     */
    public function update(Request $request, GalleryImage $galleryImage): JsonResponse
    {
        try {
            $validated = $request->validate([
                'image_url' => ['sometimes', 'string', 'max:2048'],
                'alt_text' => ['nullable', 'string', 'max:255'],
                'sort_order' => ['sometimes', 'integer'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $galleryImage->update($validated);

            $res = [
                'success' => true,
                'data' => new GalleryImageResource($galleryImage),
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
     * Admin: delete a gallery image.
     */
    public function destroy(GalleryImage $galleryImage): JsonResponse
    {
        try {
            $galleryImage->delete();

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

<?php

namespace Modules\Content\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Content\Models\NavigationLink;
use Modules\Content\Transformers\NavigationLinkResource;

class NavigationLinkController extends Controller
{
    /**
     * Public: list active navigation links, ordered for display.
     */
    public function index(): JsonResponse
    {
        try {
            $links = NavigationLink::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $res = [
                'success' => true,
                'data' => NavigationLinkResource::collection($links),
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
     * Admin: create a navigation link.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'label' => ['required', 'string', 'max:255'],
                'href' => ['required', 'string', 'max:255'],
                'sort_order' => ['sometimes', 'integer'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $link = NavigationLink::create($validated);

            $res = [
                'success' => true,
                'data' => new NavigationLinkResource($link),
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
     * Admin: update a navigation link.
     */
    public function update(Request $request, NavigationLink $navigationLink): JsonResponse
    {
        try {
            $validated = $request->validate([
                'label' => ['sometimes', 'string', 'max:255'],
                'href' => ['sometimes', 'string', 'max:255'],
                'sort_order' => ['sometimes', 'integer'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $navigationLink->update($validated);

            $res = [
                'success' => true,
                'data' => new NavigationLinkResource($navigationLink),
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
     * Admin: delete a navigation link.
     */
    public function destroy(NavigationLink $navigationLink): JsonResponse
    {
        try {
            $navigationLink->delete();

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

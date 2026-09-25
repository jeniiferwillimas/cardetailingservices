<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Leads\Models\Lead;
use Modules\Leads\Transformers\LeadResource;

class LeadController extends Controller
{
    /**
     * Public: capture a visitor's contact details for follow-up.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'source' => ['nullable', 'string', 'max:255'],
            ]);

            $lead = Lead::create($validated);

            $res = [
                'success' => true,
                'data' => new LeadResource($lead),
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
     * Admin: list captured leads, most recent first.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        try {
            $leads = Lead::orderByDesc('created_at')->paginate((int) $request->query('per_page', 20));

            $res = [
                'success' => true,
                'data' => LeadResource::collection($leads->items()),
                'meta' => [
                    'currentPage' => $leads->currentPage(),
                    'lastPage' => $leads->lastPage(),
                    'perPage' => $leads->perPage(),
                    'total' => $leads->total(),
                ],
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
     * Admin: update a lead's contact details.
     */
    public function update(Request $request, Lead $lead): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
            ]);

            $lead->update($validated);

            $res = [
                'success' => true,
                'data' => new LeadResource($lead),
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
     * Admin: delete a lead.
     */
    public function destroy(Lead $lead): JsonResponse
    {
        try {
            $lead->delete();

            $res = ['success' => true];
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

<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\UsStates;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Booking\Models\Booking;
use Modules\Booking\Transformers\BookingResource;
use Modules\Service\Models\Service;

class BookingController extends Controller
{
    /**
     * Public: submit a booking request. No payment is collected here —
     * bookings are confirmed and paid for through the live chat instead.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'customer_name' => ['required', 'string', 'max:255'],
                'customer_email' => ['required', 'email'],
                'customer_phone' => ['nullable', 'string', 'max:50'],
                'address' => ['required', 'string', 'max:255'],
                'state' => ['required', 'string', Rule::in(UsStates::codes())],
                'vehicle_info' => ['nullable', 'string', 'max:255'],
                'scheduled_for' => ['required', 'date'],
                'notes' => ['nullable', 'string'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.service_id' => ['required', 'integer', 'exists:services,id'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
            ]);

            $services = Service::query()
                ->whereIn('id', collect($validated['items'])->pluck('service_id'))
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            foreach ($validated['items'] as $item) {
                if (! $services->has($item['service_id'])) {
                    $res = ['success' => false, 'message' => 'One of the selected services is unavailable.'];

                    return response()->json($res, 422);
                }
            }

            $orderReference = (string) Str::uuid();

            DB::transaction(function () use ($validated, $services, $orderReference) {
                foreach ($validated['items'] as $item) {
                    $service = $services->get($item['service_id']);
                    for ($i = 0; $i < $item['quantity']; $i++) {
                        Booking::create([
                            'customer_name' => $validated['customer_name'],
                            'customer_email' => $validated['customer_email'],
                            'customer_phone' => $validated['customer_phone'] ?? null,
                            'address' => $validated['address'],
                            'state' => $validated['state'],
                            'vehicle_info' => $validated['vehicle_info'] ?? null,
                            'scheduled_for' => $validated['scheduled_for'],
                            'notes' => $validated['notes'] ?? null,
                            'service_id' => $service->id,
                            'order_reference' => $orderReference,
                            'payment_status' => 'unpaid',
                        ]);
                    }
                }
            });

            $res = [
                'success' => true,
                'data' => ['orderReference' => $orderReference],
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
     * Admin: list bookings, optionally filtered by status.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $bookings = Booking::query()
                ->with('service')
                ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
                ->orderBy('scheduled_for')
                ->get();

            $res = [
                'success' => true,
                'data' => BookingResource::collection($bookings),
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
     * Admin: update a booking's status.
     */
    public function updateStatus(Request $request, Booking $booking): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => ['required', Rule::in(['PENDING', 'CONFIRMED', 'COMPLETED', 'CANCELLED'])],
            ]);

            $booking->update($validated);

            $res = [
                'success' => true,
                'data' => new BookingResource($booking->load('service')),
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

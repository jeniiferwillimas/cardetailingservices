<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Booking\Models\Booking;

class BookingController extends Controller
{
    /**
     * Public: create a booking. No authentication required.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'customer_name' => ['required', 'string', 'max:255'],
                'customer_email' => ['required', 'email'],
                'customer_phone' => ['required', 'string', 'max:50'],
                'vehicle_info' => ['nullable', 'string', 'max:255'],
                'scheduled_for' => ['required', 'date'],
                'notes' => ['nullable', 'string'],
                'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
            ]);

            $booking = Booking::create($validated);

            $res = [
                'success' => true,
                'data' => $booking->load('service'),
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
                'data' => $bookings,
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
                'data' => $booking,
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

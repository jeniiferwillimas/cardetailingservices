<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\UsStates;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Booking\Models\Booking;
use Modules\Payment\Models\Payment;
use Modules\Payment\Services\NowPaymentsClient;
use Modules\Service\Models\Service;

class PaymentController extends Controller
{
    public function __construct(private NowPaymentsClient $nowPayments) {}

    /**
     * Public: check out a cart. Creates unpaid bookings + a payment record,
     * then creates a NOWPayments invoice and returns its hosted checkout URL.
     */
    public function checkout(Request $request): JsonResponse
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

            if (! $this->nowPayments->isConfigured()) {
                $res = [
                    'success' => false,
                    'message' => 'Payment is not set up yet. Please call us to complete your booking.',
                ];

                return response()->json($res, 503);
            }

            $services = Service::query()
                ->whereIn('id', collect($validated['items'])->pluck('service_id'))
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            $total = 0;
            foreach ($validated['items'] as $item) {
                $service = $services->get($item['service_id']);
                if (! $service) {
                    $res = ['success' => false, 'message' => 'One of the selected services is unavailable.'];

                    return response()->json($res, 422);
                }
                $total += $service->price * $item['quantity'];
            }

            $orderReference = (string) Str::uuid();

            DB::transaction(function () use ($validated, $services, $orderReference, $total) {
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

                Payment::create([
                    'order_reference' => $orderReference,
                    'amount' => $total,
                    'currency' => config('nowpayments.price_currency'),
                    'status' => 'pending',
                ]);
            });

            // Outside the DB transaction: an external HTTP call shouldn't hold
            // a transaction open. If it fails, undo the records we just made.
            try {
                $response = $this->nowPayments->createInvoice(
                    $orderReference,
                    $total,
                    "Car detailing booking {$orderReference}"
                );
            } catch (Exception $e) {
                Booking::where('order_reference', $orderReference)->delete();
                Payment::where('order_reference', $orderReference)->delete();
                throw $e;
            }

            Payment::where('order_reference', $orderReference)->update([
                'provider_invoice_id' => $response['id'] ?? null,
                'raw_response' => $response,
            ]);

            $res = [
                'success' => true,
                'data' => [
                    'orderReference' => $orderReference,
                    'invoiceUrl' => $response['invoice_url'] ?? null,
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
     * NOWPayments IPN webhook: verifies the signature, then marks the
     * payment (and its bookings) paid once the payment is finished/confirmed.
     */
    public function ipn(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            $signature = $request->header('x-nowpayments-sig', '');

            if (! $this->nowPayments->verifyIpnSignature($payload, $signature)) {
                return response()->json(['success' => false, 'message' => 'Invalid signature.'], 403);
            }

            $payment = Payment::where('order_reference', $payload['order_id'] ?? null)->first();
            if (! $payment) {
                return response()->json(['success' => false, 'message' => 'Unknown order.'], 404);
            }

            $status = $payload['payment_status'] ?? $payment->status;
            $payment->update([
                'status' => $status,
                'provider_payment_id' => $payload['payment_id'] ?? $payment->provider_payment_id,
                'raw_response' => $payload,
            ]);

            if (in_array($status, ['finished', 'confirmed'], true)) {
                Booking::where('order_reference', $payment->order_reference)->update([
                    'payment_status' => 'paid',
                    'status' => 'CONFIRMED',
                ]);
            }

            $res = ['success' => true];
        } catch (Exception $e) {
            $res = ['success' => false, 'message' => $e->getMessage()];
        } catch (\Throwable $t) {
            $res = ['success' => false, 'message' => $t->getMessage()];
        }

        return response()->json($res);
    }
}

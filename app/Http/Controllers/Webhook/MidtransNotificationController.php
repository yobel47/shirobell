<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/** POST /webhooks/midtrans. CSRF-exempt; trust comes from signature + status API. */
class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, PaymentService $payments): JsonResponse
    {
        $payload = $request->json()->all() ?: $request->all();

        try {
            $result = $payments->handleNotification($payload);
        } catch (Throwable $e) {
            Log::error('Midtrans notification failed', ['order_id' => $payload['order_id'] ?? null, 'error' => $e->getMessage()]);

            // Non-2xx makes Midtrans retry later.
            return response()->json(['status' => 'error'], 500);
        }

        return match ($result) {
            PaymentService::RESULT_INVALID_SIGNATURE => response()->json(['status' => 'invalid signature'], 403),
            default => response()->json(['status' => $result]),
        };
    }
}

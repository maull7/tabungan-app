<?php

namespace App\Http\Controllers;

use App\Actions\CompleteDepositPayment;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MidtransWebhookController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService,
        protected CompleteDepositPayment $completeDepositPayment,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! $this->midtransService->verifySignature($payload)) {
            Log::warning('Midtrans signature verification failed', $payload);

            return response()->json(['message' => 'Invalid signature'], Response::HTTP_FORBIDDEN);
        }

        $transaction = Transaction::where('payment_reference', $payload['order_id'] ?? null)->first();

        if (! $transaction) {
            Log::warning('Midtrans transaction not found', $payload);

            return response()->json(['message' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }

        $status = $payload['transaction_status'] ?? null;

        if (in_array($status, ['settlement', 'capture'], true)) {
            $this->completeDepositPayment->handle($transaction);
        }

        if (in_array($status, ['expire', 'cancel', 'deny'], true)) {
            $transaction->update(['payment_status' => Transaction::STATUS_FAILED]);
        }

        return response()->json(['message' => 'ok']);
    }
}

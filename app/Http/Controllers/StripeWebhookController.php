<?php

namespace App\Http\Controllers;

use App\Services\StripeBillingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StripeWebhookController extends Controller
{
    public function __construct(private readonly StripeBillingService $billing)
    {
    }

    public function handle(Request $request)
    {
        try {
            return response()->json(
                $this->billing->handleWebhook($request->getContent(), $request->header('Stripe-Signature'))
            );
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], 422);
        }
    }
}

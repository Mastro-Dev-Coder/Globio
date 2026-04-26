<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\StripeBillingService;
use Illuminate\Http\Request;

class PremiumSubscriptionController extends Controller
{
    public function __construct(private readonly StripeBillingService $billing)
    {
    }

    public function plans()
    {
        return response()->json([
            'data' => [$this->billing->premiumPlan()],
        ]);
    }

    public function status(Request $request)
    {
        $user = $request->user()->load('premiumSubscriptions');
        $subscription = $user->activePremiumSubscription();

        return response()->json([
            'active' => $user->hasActivePremium(),
            'plan' => $subscription?->only([
                'id',
                'plan_code',
                'plan_name',
                'status',
                'billing_interval',
                'amount',
                'currency',
                'cancel_at_period_end',
            ]),
            'features' => $user->premiumCapabilities(),
            'premium_access_ends_at' => optional($user->premium_access_ends_at)?->toIso8601String(),
            'badge' => $user->premiumBadge(),
            'current_period_end' => optional($subscription?->current_period_end)?->toIso8601String(),
        ]);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'success_url' => ['nullable', 'url', 'max:2048'],
            'cancel_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $successUrl = $validated['success_url'] ?? route('billing.premium.success');
        $cancelUrl = $validated['cancel_url'] ?? route('billing.premium.cancel');

        $session = $this->billing->createCheckoutSession($request->user(), $successUrl, $cancelUrl);

        return response()->json([
            'message' => 'Checkout session created.',
            'checkout_url' => $session['url'] ?? null,
            'session_id' => $session['id'] ?? null,
        ], 201);
    }

    public function portal(Request $request)
    {
        $validated = $request->validate([
            'return_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $portal = $this->billing->createCustomerPortalSession(
            $request->user(),
            $validated['return_url'] ?? route('billing.premium.portal-return')
        );

        return response()->json([
            'portal_url' => $portal['url'] ?? null,
        ]);
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string', 'max:255'],
        ]);

        $subscription = $this->billing->syncCheckoutSession($validated['session_id']);

        return response()->json([
            'message' => 'Premium subscription synchronized.',
            'subscription' => [
                'status' => $subscription->status,
                'current_period_end' => optional($subscription->current_period_end)?->toIso8601String(),
            ],
        ]);
    }

    public function cancel(Request $request)
    {
        $subscription = $this->billing->cancelSubscription($request->user());

        return response()->json([
            'message' => 'Premium subscription will end at the current billing period.',
            'subscription' => [
                'status' => $subscription->status,
                'cancel_at_period_end' => (bool) $subscription->cancel_at_period_end,
                'current_period_end' => optional($subscription->current_period_end)?->toIso8601String(),
            ],
        ]);
    }

    public function resume(Request $request)
    {
        $subscription = $this->billing->resumeSubscription($request->user());

        return response()->json([
            'message' => 'Premium subscription automatic renewal restored.',
            'subscription' => [
                'status' => $subscription->status,
                'cancel_at_period_end' => (bool) $subscription->cancel_at_period_end,
                'current_period_end' => optional($subscription->current_period_end)?->toIso8601String(),
            ],
        ]);
    }
}

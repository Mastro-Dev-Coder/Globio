<?php

namespace App\Http\Controllers;

use App\Services\StripeBillingService;
use Illuminate\Http\Request;

class PremiumController extends Controller
{
    public function __construct(private readonly StripeBillingService $billing)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $activeSubscription = $user?->loadMissing('premiumSubscriptions')->activePremiumSubscription();

        return view('premium.index', [
            'plan' => $this->billing->premiumPlan(),
            'isConfigured' => $this->billing->isConfigured(),
            'hasPremium' => $user?->hasActivePremium() ?? false,
            'activeSubscription' => $activeSubscription,
        ]);
    }

    public function checkout(Request $request)
    {
        $session = $this->billing->createCheckoutSession(
            $request->user(),
            route('premium.success'),
            route('premium.cancel')
        );

        return redirect()->away($session['url']);
    }

    public function portal(Request $request)
    {
        $portal = $this->billing->createCustomerPortalSession(
            $request->user(),
            route('premium.index')
        );

        return redirect()->away($portal['url']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\StripeBillingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PremiumController extends Controller
{
    public function __construct(private readonly StripeBillingService $billing)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $activeSubscription = $user?->loadMissing('premiumSubscriptions')->activePremiumSubscription();
        $subscriptionState = $this->subscriptionState($activeSubscription);

        return view('premium.index', [
            'plan' => $this->billing->premiumPlan(),
            'isConfigured' => $this->billing->isConfigured(),
            'hasPremium' => $user?->hasActivePremium() ?? false,
            'activeSubscription' => $activeSubscription,
            'subscriptionState' => $subscriptionState,
        ]);
    }

    public function checkout(Request $request)
    {
        try {
            $session = $this->billing->createCheckoutSession(
                $request->user(),
                route('billing.premium.success'),
                route('billing.premium.cancel')
            );
        } catch (ValidationException $exception) {
            return redirect()->route('premium.index')
                ->withErrors($exception->errors())
                ->withInput();
        }

        return redirect()->away($session['url']);
    }

    public function portal(Request $request)
    {
        try {
            $portal = $this->billing->createCustomerPortalSession(
                $request->user(),
                route('premium.index')
            );
        } catch (ValidationException $exception) {
            return redirect()->route('premium.index')
                ->withErrors($exception->errors())
                ->withInput();
        }

        return redirect()->away($portal['url']);
    }

    public function cancel(Request $request)
    {
        try {
            $this->billing->cancelSubscription($request->user());
        } catch (ValidationException $exception) {
            return redirect()->route('premium.index')
                ->withErrors($exception->errors())
                ->withInput();
        }

        return redirect()->route('premium.index')
            ->with('success', 'Il rinnovo automatico e stato disattivato. Rimani premium fino alla fine del periodo corrente.');
    }

    public function resume(Request $request)
    {
        try {
            $this->billing->resumeSubscription($request->user());
        } catch (ValidationException $exception) {
            return redirect()->route('premium.index')
                ->withErrors($exception->errors())
                ->withInput();
        }

        return redirect()->route('premium.index')
            ->with('success', 'Il rinnovo automatico e stato riattivato.');
    }

    private function subscriptionState($activeSubscription): array
    {
        if (!$activeSubscription) {
            return [
                'tone' => 'default',
                'title' => 'Premium non attivo',
                'message' => 'Attiva Globio Premium per rimuovere le pubblicita e sbloccare le funzioni extra.',
            ];
        }

        if ($activeSubscription->cancel_at_period_end) {
            return [
                'tone' => 'warning',
                'title' => 'Premium in scadenza',
                'message' => 'Hai disdetto il rinnovo automatico. L\'accesso premium resta attivo fino alla fine del periodo corrente.',
            ];
        }

        return [
            'tone' => 'success',
            'title' => 'Premium attivo',
            'message' => 'Il tuo abbonamento e attivo con rinnovo automatico.',
        ];
    }
}

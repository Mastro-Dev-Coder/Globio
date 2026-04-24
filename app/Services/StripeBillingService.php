<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\PremiumSubscription;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class StripeBillingService
{
    private const STRIPE_API_BASE = 'https://api.stripe.com/v1';
    private const STRIPE_API_VERSION = '2026-02-25.clover';

    public function premiumPlan(): array
    {
        $amount = (int) Setting::getValue('stripe_premium_amount', 1199);
        $currency = strtolower((string) Setting::getValue('stripe_premium_currency', 'eur'));

        return [
            'code' => 'globio-premium',
            'name' => 'Globio Premium',
            'interval' => 'month',
            'amount' => $amount,
            'currency' => $currency,
            'formatted_price' => number_format($amount / 100, 2, ',', '.') . ' ' . strtoupper($currency),
            'features' => $this->premiumFeatures(),
        ];
    }

    public function premiumFeatures(): array
    {
        return [
            'ad_free' => true,
            'background_playback' => true,
            'picture_in_picture' => true,
            'smart_downloads' => true,
            'higher_quality_streaming' => true,
            'reels_enhanced_controls' => true,
            'queue_management' => true,
        ];
    }

    public function createCheckoutSession(User $user, string $successUrl, string $cancelUrl): array
    {
        $secret = $this->secret();
        $priceId = Setting::getValue('stripe_premium_price_id');

        if (!$secret || !$priceId) {
            throw ValidationException::withMessages([
                'billing' => 'Stripe is not configured in admin settings.',
            ]);
        }

        $customerId = $user->stripe_customer_id ?: $this->createCustomer($user);

        if (!$user->stripe_customer_id) {
            $user->forceFill(['stripe_customer_id' => $customerId])->save();
        }

        $response = $this->request()->asForm()->post(self::STRIPE_API_BASE . '/checkout/sessions', [
            'mode' => 'subscription',
            'customer' => $customerId,
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'line_items[0][price]' => $priceId,
            'line_items[0][quantity]' => 1,
            'allow_promotion_codes' => 'true',
            'client_reference_id' => (string) $user->id,
            'metadata[user_id]' => (string) $user->id,
            'metadata[plan_code]' => 'globio-premium',
        ]);

        $payload = $this->decode($response);

        PremiumSubscription::updateOrCreate(
            ['stripe_checkout_session_id' => $payload['id']],
            [
                'user_id' => $user->id,
                'provider' => 'stripe',
                'stripe_customer_id' => $customerId,
                'stripe_price_id' => $priceId,
                'plan_code' => 'globio-premium',
                'plan_name' => 'Globio Premium',
                'status' => PremiumSubscription::STATUS_PENDING,
                'billing_interval' => 'month',
                'amount' => (int) ($this->premiumPlan()['amount']),
                'currency' => $this->premiumPlan()['currency'],
                'features' => $this->premiumFeatures(),
                'meta' => [
                    'checkout_url' => $payload['url'] ?? null,
                ],
            ]
        );

        return $payload;
    }

    public function createCustomerPortalSession(User $user, string $returnUrl): array
    {
        $secret = $this->secret();
        $customerId = $user->stripe_customer_id;

        if (!$secret || !$customerId) {
            throw ValidationException::withMessages([
                'billing' => 'Customer portal is unavailable for this user.',
            ]);
        }

        $response = $this->request()->asForm()->post(self::STRIPE_API_BASE . '/billing_portal/sessions', [
            'customer' => $customerId,
            'return_url' => $returnUrl,
        ]);

        return $this->decode($response);
    }

    public function syncCheckoutSession(string $sessionId): PremiumSubscription
    {
        $response = $this->request()->get(self::STRIPE_API_BASE . '/checkout/sessions/' . $sessionId, [
            'expand' => ['subscription'],
        ]);

        $session = $this->decode($response);
        $subscriptionData = Arr::get($session, 'subscription');

        if (!is_array($subscriptionData)) {
            throw ValidationException::withMessages([
                'billing' => 'Stripe checkout session does not contain a subscription yet.',
            ]);
        }

        return $this->syncSubscriptionPayload($subscriptionData, $sessionId);
    }

    public function handleWebhook(string $payload, ?string $signatureHeader): array
    {
        $event = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        $this->assertValidWebhookSignature($payload, $signatureHeader);

        $type = $event['type'] ?? '';
        $object = $event['data']['object'] ?? [];

        $subscription = null;

        if (in_array($type, [
            'checkout.session.completed',
            'customer.subscription.created',
            'customer.subscription.updated',
            'customer.subscription.deleted',
            'invoice.payment_failed',
            'invoice.paid',
        ], true)) {
            if ($type === 'checkout.session.completed' && !empty($object['subscription'])) {
                $subscription = $this->syncStripeSubscriptionId((string) $object['subscription'], (string) ($object['id'] ?? ''));
            } elseif (!empty($object['subscription'])) {
                $subscription = $this->syncStripeSubscriptionId((string) $object['subscription']);
            } elseif (($object['object'] ?? null) === 'subscription') {
                $subscription = $this->syncSubscriptionPayload($object);
            }
        }

        return [
            'received' => true,
            'type' => $type,
            'subscription_id' => $subscription?->id,
        ];
    }

    private function syncStripeSubscriptionId(string $stripeSubscriptionId, ?string $checkoutSessionId = null): PremiumSubscription
    {
        $response = $this->request()->get(self::STRIPE_API_BASE . '/subscriptions/' . $stripeSubscriptionId);
        $payload = $this->decode($response);

        return $this->syncSubscriptionPayload($payload, $checkoutSessionId);
    }

    private function syncSubscriptionPayload(array $payload, ?string $checkoutSessionId = null): PremiumSubscription
    {
        $customerId = (string) ($payload['customer'] ?? '');
        $subscriptionId = (string) ($payload['id'] ?? '');

        $subscription = PremiumSubscription::query()
            ->when($subscriptionId !== '', fn ($query) => $query->orWhere('stripe_subscription_id', $subscriptionId))
            ->when($checkoutSessionId, fn ($query) => $query->orWhere('stripe_checkout_session_id', $checkoutSessionId))
            ->when($customerId !== '', fn ($query) => $query->orWhere('stripe_customer_id', $customerId))
            ->latest('id')
            ->first();

        $user = $subscription?->user;

        if (!$user && $customerId !== '') {
            $user = User::where('stripe_customer_id', $customerId)->first();
        }

        if (!$user && !empty($payload['metadata']['user_id'])) {
            $user = User::find((int) $payload['metadata']['user_id']);
        }

        if (!$user) {
            throw ValidationException::withMessages([
                'billing' => 'Unable to match Stripe subscription to a user.',
            ]);
        }

        $plan = $this->premiumPlan();
        $priceData = Arr::first($payload['items']['data'] ?? []);
        $priceId = $priceData['price']['id'] ?? null;
        $amount = (int) ($priceData['price']['unit_amount'] ?? $plan['amount']);
        $currency = strtolower((string) ($priceData['price']['currency'] ?? $plan['currency']));
        $interval = (string) ($priceData['price']['recurring']['interval'] ?? $plan['interval']);

        $record = PremiumSubscription::updateOrCreate(
            ['stripe_subscription_id' => $subscriptionId],
            [
                'user_id' => $user->id,
                'provider' => 'stripe',
                'stripe_customer_id' => $customerId ?: $user->stripe_customer_id,
                'stripe_price_id' => $priceId,
                'stripe_checkout_session_id' => $checkoutSessionId,
                'plan_code' => 'globio-premium',
                'plan_name' => 'Globio Premium',
                'status' => (string) ($payload['status'] ?? PremiumSubscription::STATUS_PENDING),
                'billing_interval' => $interval ?: 'month',
                'amount' => $amount,
                'currency' => $currency ?: 'eur',
                'cancel_at_period_end' => (bool) ($payload['cancel_at_period_end'] ?? false),
                'current_period_start' => $this->asCarbon($payload['current_period_start'] ?? null),
                'current_period_end' => $this->asCarbon($payload['current_period_end'] ?? null),
                'trial_ends_at' => $this->asCarbon($payload['trial_end'] ?? null),
                'ends_at' => $this->asCarbon($payload['ended_at'] ?? null),
                'last_webhook_at' => now(),
                'features' => $this->premiumFeatures(),
                'meta' => [
                    'latest_payload_status' => $payload['status'] ?? null,
                ],
            ]
        );

        $user->forceFill([
            'stripe_customer_id' => $record->stripe_customer_id ?: $user->stripe_customer_id,
            'premium_access_ends_at' => $record->isActive() ? $record->current_period_end : $record->ends_at,
        ])->save();

        return $record;
    }

    private function createCustomer(User $user): string
    {
        $response = $this->request()->asForm()->post(self::STRIPE_API_BASE . '/customers', [
            'email' => $user->email,
            'name' => $user->name,
            'metadata[user_id]' => (string) $user->id,
        ]);

        return (string) ($this->decode($response)['id'] ?? '');
    }

    private function assertValidWebhookSignature(string $payload, ?string $signatureHeader): void
    {
        $secret = Setting::getValue('stripe_webhook_secret');

        if (!$secret) {
            throw ValidationException::withMessages([
                'stripe' => 'Missing Stripe webhook secret.',
            ]);
        }

        if (!$signatureHeader) {
            throw ValidationException::withMessages([
                'stripe' => 'Missing Stripe signature header.',
            ]);
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $segment) {
            [$key, $value] = array_pad(explode('=', $segment, 2), 2, null);
            if ($key && $value) {
                $parts[$key] = $value;
            }
        }

        $timestamp = $parts['t'] ?? null;
        $signature = $parts['v1'] ?? null;

        if (!$timestamp || !$signature) {
            throw ValidationException::withMessages([
                'stripe' => 'Malformed Stripe signature header.',
            ]);
        }

        if (abs(now()->timestamp - (int) $timestamp) > 300) {
            throw ValidationException::withMessages([
                'stripe' => 'Expired Stripe webhook signature.',
            ]);
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        if (!hash_equals($expected, $signature)) {
            throw ValidationException::withMessages([
                'stripe' => 'Invalid Stripe webhook signature.',
            ]);
        }
    }

    private function request()
    {
        return Http::withBasicAuth($this->secret(), '')
            ->acceptJson()
            ->withHeaders([
                'Stripe-Version' => self::STRIPE_API_VERSION,
            ]);
    }

    public function isConfigured(): bool
    {
        return $this->secret() !== '' && Setting::getValue('stripe_premium_price_id') !== null;
    }

    private function decode(Response $response): array
    {
        $response->throw();

        return $response->json();
    }

    private function asCarbon(mixed $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::createFromTimestamp((int) $value);
    }

    private function secret(): string
    {
        return (string) Setting::getValue('stripe_secret_key', '');
    }
};

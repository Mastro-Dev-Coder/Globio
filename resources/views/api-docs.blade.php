<x-layout>
    @php
        $baseUrl = url('/api/v1');
        $groups = [
            [
                'title' => 'Auth',
                'description' => 'Registrazione, login e logout token-based per app e client mobile.',
                'endpoints' => [
                    ['method' => 'POST', 'path' => '/auth/register', 'auth' => 'No', 'params' => 'name, email, password, password_confirmation, device_name?'],
                    ['method' => 'POST', 'path' => '/auth/login', 'auth' => 'No', 'params' => 'email, password, device_name?'],
                    ['method' => 'POST', 'path' => '/auth/logout', 'auth' => 'Si', 'params' => 'Bearer token'],
                ],
            ],
            [
                'title' => 'Public Content',
                'description' => 'Feed, creators, ricerca e configurazione globale dell’app.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/app-config', 'auth' => 'No', 'params' => 'feature flags, premium plan, app config'],
                    ['method' => 'GET', 'path' => '/home', 'auth' => 'No', 'params' => 'limit? (4-24)'],
                    ['method' => 'GET', 'path' => '/videos', 'auth' => 'No', 'params' => 'type?, sort?, q?, creator?, per_page?'],
                    ['method' => 'GET', 'path' => '/videos/{id_or_slug}', 'auth' => 'No', 'params' => 'video detail'],
                    ['method' => 'GET', 'path' => '/videos/{id_or_slug}/comments', 'auth' => 'No', 'params' => 'commenti approvati'],
                    ['method' => 'GET', 'path' => '/creators', 'auth' => 'No', 'params' => 'q?, per_page?'],
                    ['method' => 'GET', 'path' => '/creators/{id_or_username_or_channel_slug}', 'auth' => 'No', 'params' => 'creator detail'],
                    ['method' => 'GET', 'path' => '/creators/{id_or_username_or_channel_slug}/videos', 'auth' => 'No', 'params' => 'type?, per_page?'],
                    ['method' => 'GET', 'path' => '/search', 'auth' => 'No', 'params' => 'q, limit?'],
                ],
            ],
            [
                'title' => 'Premium',
                'description' => 'Abbonamento mensile sicuro in stile YouTube con Stripe Checkout e Customer Portal.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/premium/plans', 'auth' => 'No', 'params' => 'piano premium disponibile'],
                    ['method' => 'GET', 'path' => '/me/premium', 'auth' => 'Si', 'params' => 'stato premium, capabilities, scadenza'],
                    ['method' => 'POST', 'path' => '/me/premium/checkout', 'auth' => 'Si', 'params' => 'success_url?, cancel_url?'],
                    ['method' => 'POST', 'path' => '/me/premium/confirm', 'auth' => 'Si', 'params' => 'session_id Stripe Checkout'],
                    ['method' => 'POST', 'path' => '/me/premium/portal', 'auth' => 'Si', 'params' => 'return_url?'],
                ],
            ],
            [
                'title' => 'Account & Library',
                'description' => 'Profilo utente, watch later, cronologia, notifiche e playlist.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/me', 'auth' => 'Si', 'params' => 'profilo con stato premium'],
                    ['method' => 'PUT', 'path' => '/me', 'auth' => 'Si', 'params' => 'name?, channel_name?, channel_description?, country?'],
                    ['method' => 'GET', 'path' => '/me/watch-later', 'auth' => 'Si', 'params' => '-'],
                    ['method' => 'POST', 'path' => '/me/watch-later/{id_or_slug}', 'auth' => 'Si', 'params' => 'aggiungi watch later'],
                    ['method' => 'DELETE', 'path' => '/me/watch-later/{id_or_slug}', 'auth' => 'Si', 'params' => 'rimuovi watch later'],
                    ['method' => 'GET', 'path' => '/me/history', 'auth' => 'Si', 'params' => '-'],
                    ['method' => 'POST', 'path' => '/me/history/{id_or_slug}', 'auth' => 'Si', 'params' => 'watched_duration, total_duration?, completed?'],
                    ['method' => 'GET', 'path' => '/me/notifications', 'auth' => 'Si', 'params' => 'lista notifiche'],
                ],
            ],
            [
                'title' => 'Playlists',
                'description' => 'CRUD playlist dedicato all’app mobile e web autenticato.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/me/playlists', 'auth' => 'Si', 'params' => 'lista playlist utente'],
                    ['method' => 'POST', 'path' => '/me/playlists', 'auth' => 'Si', 'params' => 'title, description?, is_public?, video_ids[]?'],
                    ['method' => 'GET', 'path' => '/me/playlists/{playlist}', 'auth' => 'Si', 'params' => 'dettaglio playlist con video'],
                    ['method' => 'PUT', 'path' => '/me/playlists/{playlist}', 'auth' => 'Si', 'params' => 'title?, description?, is_public?'],
                    ['method' => 'DELETE', 'path' => '/me/playlists/{playlist}', 'auth' => 'Si', 'params' => 'elimina playlist'],
                    ['method' => 'POST', 'path' => '/me/playlists/{playlist}/videos', 'auth' => 'Si', 'params' => 'video_id'],
                    ['method' => 'DELETE', 'path' => '/me/playlists/{playlist}/videos/{video}', 'auth' => 'Si', 'params' => 'rimuovi video'],
                ],
            ],
            [
                'title' => 'Creator & Studio',
                'description' => 'Canale, analytics, community, feedback e upload contenuti.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/me/channel', 'auth' => 'Si', 'params' => 'info canale'],
                    ['method' => 'PUT', 'path' => '/me/channel', 'auth' => 'Si', 'params' => 'channel_name?, channel_description?, country?, social_links?'],
                    ['method' => 'GET', 'path' => '/me/studio/summary', 'auth' => 'Si', 'params' => 'start_date?, end_date?'],
                    ['method' => 'GET', 'path' => '/me/studio/analytics', 'auth' => 'Si', 'params' => 'start_date?, end_date?, limit?'],
                    ['method' => 'GET', 'path' => '/me/studio/community', 'auth' => 'Si', 'params' => 'status?, per_page?'],
                    ['method' => 'GET', 'path' => '/me/studio/reports', 'auth' => 'Si', 'params' => 'view?, status?, per_page?'],
                    ['method' => 'POST', 'path' => '/videos/upload', 'auth' => 'Si', 'params' => 'title, video_file, thumbnail?, description?, is_public?, is_reel?, tags?'],
                    ['method' => 'POST', 'path' => '/reels/upload', 'auth' => 'Si', 'params' => 'title, video_file, thumbnail?, description?'],
                ],
            ],
        ];

        $methodColors = [
            'GET' => 'bg-emerald-500/15 text-emerald-300 border-emerald-400/35',
            'POST' => 'bg-sky-500/15 text-sky-300 border-sky-400/35',
            'PUT' => 'bg-amber-500/15 text-amber-300 border-amber-400/35',
            'DELETE' => 'bg-rose-500/15 text-rose-300 border-rose-400/35',
        ];
    @endphp

    <section class="min-h-screen bg-slate-950 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="relative overflow-hidden rounded-3xl border border-slate-700/50 bg-gradient-to-br from-slate-900 via-slate-900 to-cyan-950 p-6 shadow-2xl shadow-black/30 sm:p-10">
                <div class="pointer-events-none absolute -top-20 -right-20 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-20 -left-16 h-72 w-72 rounded-full bg-sky-500/15 blur-3xl"></div>

                <div class="relative">
                    <p class="inline-flex items-center rounded-full border border-slate-600 bg-slate-900/80 px-3 py-1 text-xs font-semibold tracking-wide text-slate-200">
                        API Docs
                    </p>
                    <h1 class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-4xl">Globio API v1</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300 sm:text-base">
                        Endpoint pronti per web e app Flutter, con abbonamento premium mensile, playlist CRUD e stato premium integrato nel profilo utente.
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl border border-slate-700/60 bg-slate-900/60 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Base URL</p>
                            <p class="mt-2 break-all font-mono text-sm text-cyan-300">{{ $baseUrl }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-700/60 bg-slate-900/60 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Auth Header</p>
                            <p class="mt-2 font-mono text-sm text-cyan-300">Authorization: Bearer &lt;token&gt;</p>
                        </div>
                        <div class="rounded-2xl border border-slate-700/60 bg-slate-900/60 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Billing</p>
                            <p class="mt-2 text-sm text-slate-200">Stripe Checkout + Customer Portal</p>
                        </div>
                        <div class="rounded-2xl border border-slate-700/60 bg-slate-900/60 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Formato</p>
                            <p class="mt-2 font-mono text-sm text-cyan-300">application/json</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-emerald-900/60 bg-emerald-950/20 p-5">
                    <h2 class="text-base font-semibold text-emerald-200">Premium Features</h2>
                    <ul class="mt-3 space-y-2 text-sm text-emerald-100/90">
                        <li>Video e reels senza pubblicita</li>
                        <li>Background playback e picture in picture</li>
                        <li>Qualita avanzata e controlli premium</li>
                        <li>Customer portal per gestione mensile sicura</li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-sky-900/60 bg-sky-950/20 p-5">
                    <h2 class="text-base font-semibold text-sky-200">Playlist API</h2>
                    <ul class="mt-3 space-y-2 text-sm text-sky-100/90">
                        <li>Creazione playlist</li>
                        <li>Aggiunta e rimozione video</li>
                        <li>Visibilita pubblica o privata</li>
                        <li>Payload pronto per mobile app</li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-amber-900/60 bg-amber-950/20 p-5">
                    <h2 class="text-base font-semibold text-amber-200">Webhook</h2>
                    <p class="mt-3 font-mono text-xs text-amber-100">POST {{ url('/billing/stripe/webhook') }}</p>
                    <p class="mt-3 text-sm text-amber-100/90">Da collegare agli eventi Stripe di checkout, rinnovo, cancellazione e pagamenti falliti.</p>
                </div>
            </div>

            <div class="mt-8 space-y-6">
                @foreach ($groups as $group)
                    <article class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg shadow-black/20">
                        <div class="border-b border-slate-800 bg-slate-900/80 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-semibold text-white">{{ $group['title'] }}</h2>
                            <p class="mt-1 text-sm text-slate-400">{{ $group['description'] }}</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-800 text-sm">
                                <thead class="bg-slate-900/50 text-left text-xs uppercase tracking-wide text-slate-400">
                                    <tr>
                                        <th class="px-5 py-3 sm:px-6">Method</th>
                                        <th class="px-5 py-3 sm:px-6">Endpoint</th>
                                        <th class="px-5 py-3 sm:px-6">Auth</th>
                                        <th class="px-5 py-3 sm:px-6">Parametri</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 text-slate-200">
                                    @foreach ($group['endpoints'] as $endpoint)
                                        <tr class="transition-colors hover:bg-slate-800/35">
                                            <td class="px-5 py-4 sm:px-6">
                                                <span class="inline-flex rounded-lg border px-2.5 py-1 text-xs font-bold tracking-wide {{ $methodColors[$endpoint['method']] ?? 'bg-slate-700/25 text-slate-200 border-slate-600/35' }}">
                                                    {{ $endpoint['method'] }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 font-mono text-xs text-cyan-300 sm:px-6">{{ $endpoint['path'] }}</td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <span class="inline-flex rounded-md px-2 py-1 text-xs {{ $endpoint['auth'] === 'Si' ? 'bg-amber-500/15 text-amber-300' : 'bg-emerald-500/15 text-emerald-300' }}">
                                                    {{ $endpoint['auth'] }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 text-slate-300 sm:px-6">{{ $endpoint['params'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                    <h3 class="text-base font-semibold text-white">Esempio Premium Checkout</h3>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>curl -X POST "{{ $baseUrl }}/me/premium/checkout" \
  -H "Authorization: Bearer TOKEN_VALUE" \
  -H "Content-Type: application/json" \
  -d '{
    "success_url": "{{ url('/premium/success') }}",
    "cancel_url": "{{ url('/premium/cancel') }}"
  }'</code></pre>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                    <h3 class="text-base font-semibold text-white">Risposta Premium Status</h3>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>{
  "active": true,
  "plan": {
    "id": 3,
    "plan_code": "globio-premium",
    "plan_name": "Globio Premium",
    "status": "active",
    "billing_interval": "month",
    "amount": 1199,
    "currency": "eur",
    "cancel_at_period_end": false
  },
  "features": {
    "ad_free": true,
    "background_playback": true,
    "picture_in_picture": true,
    "smart_downloads": true,
    "higher_quality_streaming": true,
    "reels_enhanced_controls": true,
    "queue_management": true
  },
  "premium_access_ends_at": "2026-05-24T12:00:00+02:00"
}</code></pre>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                    <h3 class="text-base font-semibold text-white">Esempio Creazione Playlist</h3>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>curl -X POST "{{ $baseUrl }}/me/playlists" \
  -H "Authorization: Bearer TOKEN_VALUE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Reels da rivedere",
    "description": "Playlist personale",
    "is_public": true,
    "video_ids": [12, 18, 22]
  }'</code></pre>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                    <h3 class="text-base font-semibold text-white">Webhook Stripe Da Registrare</h3>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>checkout.session.completed
customer.subscription.created
customer.subscription.updated
customer.subscription.deleted
invoice.payment_failed
invoice.paid</code></pre>
                </div>
            </div>
        </div>
    </section>
</x-layout>

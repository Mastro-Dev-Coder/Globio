<x-layout>
    @php
        $baseUrl = url('/api/v1');
        $groups = [
            [
                'title' => 'Auth',
                'description' => 'Registrazione, login e logout token-based per app Flutter.',
                'endpoints' => [
                    [
                        'method' => 'POST',
                        'path' => '/auth/register',
                        'auth' => 'No',
                        'params' => 'name, email, password, password_confirmation, device_name?',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/auth/login',
                        'auth' => 'No',
                        'params' => 'email, password, device_name?',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/auth/logout',
                        'auth' => 'Si',
                        'params' => 'Bearer token in header',
                    ],
                ],
            ],
            [
                'title' => 'Public Content',
                'description' => 'Dati pubblici per home, feed, creators e ricerca.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/app-config', 'auth' => 'No', 'params' => '-'],
                    ['method' => 'GET', 'path' => '/home', 'auth' => 'No', 'params' => 'limit? (4-24)'],
                    [
                        'method' => 'GET',
                        'path' => '/videos',
                        'auth' => 'No',
                        'params' => 'type?, sort?, q?, creator?, per_page?',
                    ],
                    ['method' => 'GET', 'path' => '/videos/{id_or_slug}', 'auth' => 'No', 'params' => 'path param'],
                    [
                        'method' => 'GET',
                        'path' => '/videos/{id_or_slug}/comments',
                        'auth' => 'No',
                        'params' => 'path param',
                    ],
                    ['method' => 'GET', 'path' => '/creators', 'auth' => 'No', 'params' => 'q?, per_page?'],
                    [
                        'method' => 'GET',
                        'path' => '/creators/{id_or_username_or_channel_slug}',
                        'auth' => 'No',
                        'params' => 'path param',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/creators/{id_or_username_or_channel_slug}/videos',
                        'auth' => 'No',
                        'params' => 'type?, per_page?',
                    ],
                    ['method' => 'GET', 'path' => '/search', 'auth' => 'No', 'params' => 'q (required), limit?'],
                ],
            ],
            [
                'title' => 'Content Actions (Auth)',
                'description' => 'Azioni utente su video, creators, condivisioni e segnalazioni.',
                'endpoints' => [
                    [
                        'method' => 'POST',
                        'path' => '/videos/{id_or_slug}/comments',
                        'auth' => 'Si',
                        'params' => 'content, parent_id?',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/videos/{id_or_slug}/reaction',
                        'auth' => 'Si',
                        'params' => 'reaction: like|dislike|none',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/creators/{id_or_username_or_channel_slug}/subscribe',
                        'auth' => 'Si',
                        'params' => 'toggle subscribe',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/videos/{id_or_slug}/share',
                        'auth' => 'Si',
                        'params' => 'get share data',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/reports/reasons',
                        'auth' => 'Si',
                        'params' => 'target_type?: user|video|comment|channel',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/reports',
                        'auth' => 'Si',
                        'params' => 'target_type, target_id, reason, type?, description?',
                    ],
                ],
            ],
            [
                'title' => 'Account & Library (Auth)',
                'description' => 'Profilo, watch later, history, playlists.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/me', 'auth' => 'Si', 'params' => '-'],
                    [
                        'method' => 'PUT',
                        'path' => '/me',
                        'auth' => 'Si',
                        'params' => 'name?, channel_name?, channel_description?, country?',
                    ],
                    ['method' => 'GET', 'path' => '/me/watch-later', 'auth' => 'Si', 'params' => '-'],
                    [
                        'method' => 'POST',
                        'path' => '/me/watch-later/{id_or_slug}',
                        'auth' => 'Si',
                        'params' => 'path param',
                    ],
                    [
                        'method' => 'DELETE',
                        'path' => '/me/watch-later/{id_or_slug}',
                        'auth' => 'Si',
                        'params' => 'path param',
                    ],
                    ['method' => 'GET', 'path' => '/me/history', 'auth' => 'Si', 'params' => '-'],
                    [
                        'method' => 'POST',
                        'path' => '/me/history/{id_or_slug}',
                        'auth' => 'Si',
                        'params' => 'watched_duration, total_duration?, completed?',
                    ],
                    ['method' => 'GET', 'path' => '/me/playlists', 'auth' => 'Si', 'params' => '-'],
                    ['method' => 'GET', 'path' => '/me/liked', 'auth' => 'Si', 'params' => 'video piaciuti'],
                ],
            ],
            [
                'title' => 'My Channel (Auth)',
                'description' => 'Gestione canale personale, avatar, banner.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/me/channel', 'auth' => 'Si', 'params' => 'info canale completo'],
                    [
                        'method' => 'PUT',
                        'path' => '/me/channel',
                        'auth' => 'Si',
                        'params' => 'channel_name?, channel_description?, country?, social_links?',
                    ],
                    ['method' => 'PUT', 'path' => '/me/avatar', 'auth' => 'Si', 'params' => 'avatar (file image)'],
                    ['method' => 'PUT', 'path' => '/me/banner', 'auth' => 'Si', 'params' => 'banner (file image)'],
                ],
            ],
            [
                'title' => 'Subscriptions (Auth)',
                'description' => 'Iscrizioni e iscritti.',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/me/subscriptions',
                        'auth' => 'Si',
                        'params' => 'canali a cui sei iscritto',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/me/subscribers',
                        'auth' => 'Si',
                        'params' => 'iscritti al tuo canale',
                    ],
                ],
            ],
            [
                'title' => 'My Videos (Auth)',
                'description' => 'Gestione video personali.',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/me/videos',
                        'auth' => 'Si',
                        'params' => 'type?, status?, per_page?',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/videos/upload',
                        'auth' => 'Si',
                        'params' => 'title, video_file, thumbnail?, description?, is_public?, is_reel?, tags?',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/reels/upload',
                        'auth' => 'Si',
                        'params' => 'title, video_file, thumbnail?, description?',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/posts/create',
                        'auth' => 'Si',
                        'params' => 'title, video_file, thumbnail?, description?',
                    ],
                    [
                        'method' => 'PUT',
                        'path' => '/videos/{id_or_slug}',
                        'auth' => 'Si',
                        'params' =>
                            'title?, description?, is_public?, status?, comments_enabled?, likes_enabled?, tags?',
                    ],
                    [
                        'method' => 'DELETE',
                        'path' => '/videos/{id_or_slug}',
                        'auth' => 'Si',
                        'params' => 'elimina video',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/videos/{id_or_slug}/status',
                        'auth' => 'Si',
                        'params' => 'stato elaborazione',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/videos/bulk',
                        'auth' => 'Si',
                        'params' => 'action: set_public|set_private|delete, video_ids[]',
                    ],
                ],
            ],
            [
                'title' => 'Comments (Auth)',
                'description' => 'Gestione commenti.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/me/comments', 'auth' => 'Si', 'params' => 'miei commenti'],
                    [
                        'method' => 'POST',
                        'path' => '/comments/{comment}/like',
                        'auth' => 'Si',
                        'params' => 'like/unlike commento',
                    ],
                    [
                        'method' => 'DELETE',
                        'path' => '/comments/{comment}',
                        'auth' => 'Si',
                        'params' => 'elimina commento',
                    ],
                ],
            ],
            [
                'title' => 'Notifications (Auth)',
                'description' =>
                    'Gestione notifiche user. Restituisce sia notifications Laravel sia app_notifications legacy.',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/me/notifications',
                        'auth' => 'Si',
                        'params' =>
                            'response: data[], unread_count; item: id, source, title, message, type, action_url, data, read_at, created_at',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/me/notifications/read-all',
                        'auth' => 'Si',
                        'params' => 'segna lette entrambe le sorgenti',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/me/notifications/{id}/read',
                        'auth' => 'Si',
                        'params' => 'id UUID Laravel o id app_notifications; accetta anche prefissi db: / app:',
                    ],
                ],
            ],
            [
                'title' => 'Studio (Auth)',
                'description' => 'Dati creator per dashboard Studio, analytics, community, segnalazioni e feedback.',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/me/studio/summary',
                        'auth' => 'Si',
                        'params' => 'start_date?, end_date?; totals, recent_videos',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/me/studio/analytics',
                        'auth' => 'Si',
                        'params' =>
                            'start_date?, end_date?, limit?; totals, daily, top_videos, traffic_sources, demographics',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/me/studio/analytics/videos/{id_or_slug}',
                        'auth' => 'Si',
                        'params' => 'start_date?, end_date?; analytics singolo video',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/me/studio/community',
                        'auth' => 'Si',
                        'params' => 'status?: all|approved|pending|rejected|hidden, per_page?',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/me/studio/community/comments/{comment}/approve',
                        'auth' => 'Si',
                        'params' => 'approva commento sul proprio video',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/me/studio/community/comments/{comment}/reject',
                        'auth' => 'Si',
                        'params' => 'rifiuta commento sul proprio video',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/me/studio/community/comments/{comment}/hide',
                        'auth' => 'Si',
                        'params' => 'nasconde commento sul proprio video',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/me/studio/reports',
                        'auth' => 'Si',
                        'params' =>
                            'view?: received|submitted, status?: all|pending|reviewed|resolved|dismissed|escalated, per_page?',
                    ],
                    [
                        'method' => 'GET',
                        'path' => '/me/studio/feedback',
                        'auth' => 'Si',
                        'params' => 'status?: all|read|unread, per_page?',
                    ],
                    [
                        'method' => 'POST',
                        'path' => '/me/studio/feedback/{feedback}/read',
                        'auth' => 'Si',
                        'params' => 'segna feedback come letto',
                    ],
                ],
            ],
            [
                'title' => 'User Settings (Auth)',
                'description' => 'Impostazioni profilo utente.',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/me/settings', 'auth' => 'Si', 'params' => 'impostazioni account'],
                    [
                        'method' => 'PUT',
                        'path' => '/me/settings',
                        'auth' => 'Si',
                        'params' => 'email_notifications?, push_notifications?, private_profile?',
                    ],
                ],
            ],
            [
                'title' => 'App Settings (Public)',
                'description' => 'Impostazioni e configurazioni globali.',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/settings',
                        'auth' => 'No',
                        'params' => 'configurazione app completa',
                    ],
                    ['method' => 'GET', 'path' => '/settings/features', 'auth' => 'No', 'params' => 'feature flags'],
                    ['method' => 'GET', 'path' => '/settings/limits', 'auth' => 'No', 'params' => 'limiti sistema'],
                    ['method' => 'GET', 'path' => '/settings/countries', 'auth' => 'No', 'params' => 'lista paesi'],
                    [
                        'method' => 'GET',
                        'path' => '/settings/languages',
                        'auth' => 'No',
                        'params' => 'lingue supportate',
                    ],
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
        <div class="mx-auto w-full max-w-7xl">
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-700/50 bg-gradient-to-br from-slate-900 via-slate-900 to-sky-950 p-6 shadow-2xl shadow-black/30 sm:p-10">
                <div
                    class="pointer-events-none absolute -top-20 -right-20 h-72 w-72 rounded-full bg-sky-500/20 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute -bottom-20 -left-16 h-72 w-72 rounded-full bg-cyan-400/20 blur-3xl">
                </div>

                <div class="relative">
                    <p
                        class="inline-flex items-center rounded-full border border-slate-600 bg-slate-900/80 px-3 py-1 text-xs font-semibold tracking-wide text-slate-200">
                        Developer API Docs
                    </p>
                    <h1 class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-4xl">Flutter Integration API v1
                    </h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300 sm:text-base">
                        Documentazione completa endpoint, parametri e header per collegare correttamente l'app Flutter
                        al backend.
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-2xl border border-slate-700/60 bg-slate-900/60 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Base URL</p>
                            <p class="mt-2 break-all font-mono text-sm text-cyan-300">{{ $baseUrl }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-700/60 bg-slate-900/60 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Auth Header</p>
                            <p class="mt-2 font-mono text-sm text-cyan-300">Authorization: Bearer &lt;token&gt;</p>
                        </div>
                        <div class="rounded-2xl border border-slate-700/60 bg-slate-900/60 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">Formato</p>
                            <p class="mt-2 font-mono text-sm text-cyan-300">application/json</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 space-y-6">
                @foreach ($groups as $group)
                    <article
                        class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg shadow-black/20">
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
                                        <tr class="hover:bg-slate-800/35 transition-colors">
                                            <td class="px-5 py-4 sm:px-6">
                                                <span
                                                    class="inline-flex rounded-lg border px-2.5 py-1 text-xs font-bold tracking-wide {{ $methodColors[$endpoint['method']] ?? 'bg-slate-700/25 text-slate-200 border-slate-600/35' }}">
                                                    {{ $endpoint['method'] }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 font-mono text-xs text-cyan-300 sm:px-6">
                                                {{ $endpoint['path'] }}</td>
                                            <td class="px-5 py-4 sm:px-6">
                                                <span
                                                    class="inline-flex rounded-md px-2 py-1 text-xs {{ $endpoint['auth'] === 'Si' ? 'bg-amber-500/15 text-amber-300' : 'bg-emerald-500/15 text-emerald-300' }}">
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
                    <h3 class="text-base font-semibold text-white">Esempio Login</h3>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>curl -X POST "{{ $baseUrl }}/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "mastro@gmail.com",
    "password": "bmx321516",
    "device_name": "flutter-android"
  }'</code></pre>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                    <h3 class="text-base font-semibold text-white">Esempio Response Login</h3>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>{
  "message": "Login successful.",
  "token": "TOKEN_VALUE",
  "token_type": "Bearer",
  "expires_at": "2026-05-15T13:14:52+02:00",
  "user": {
    "id": 1,
    "name": "mastro",
    "email": "mastro@gmail.com"
  }
}</code></pre>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                    <h3 class="text-base font-semibold text-white">Esempio Notifiche</h3>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>curl "{{ $baseUrl }}/me/notifications" \
  -H "Authorization: Bearer TOKEN_VALUE"</code></pre>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>{
  "data": [
    {
      "id": "9d4b1b1e-...",
      "source": "database",
      "title": "Nuovo commento su: Video demo",
      "message": "Bel contenuto...",
      "type": "new_comment",
      "action_url": "https://example.com/videos/video-demo#comment-12",
      "data": {},
      "read_at": null,
      "created_at": "2026-04-23T15:45:00+02:00"
    }
  ],
  "unread_count": 1
}</code></pre>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                    <h3 class="text-base font-semibold text-white">Esempio Studio Summary</h3>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>curl "{{ $baseUrl }}/me/studio/summary?start_date=2026-04-01&end_date=2026-04-23" \
  -H "Authorization: Bearer TOKEN_VALUE"</code></pre>
                    <pre class="mt-3 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950 p-4 text-xs leading-6 text-slate-200"><code>{
  "range": {
    "start_date": "2026-04-01",
    "end_date": "2026-04-23"
  },
  "totals": {
    "videos": 12,
    "published_videos": 10,
    "subscribers": 128,
    "views": 4200,
    "likes": 310,
    "comments": 64,
    "open_reports": 1,
    "unread_feedback": 2
  },
  "recent_videos": []
}</code></pre>
                </div>
            </div>
        </div>
    </section>
</x-layout>

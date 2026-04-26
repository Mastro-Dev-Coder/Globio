<x-layout>
    <section class="relative overflow-hidden bg-slate-50 dark:bg-gray-950">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(250,204,21,0.22),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(59,130,246,0.16),transparent_32%)] dark:bg-[radial-gradient(circle_at_top_right,rgba(250,204,21,0.18),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(59,130,246,0.18),transparent_32%)]"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid items-start gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="rounded-[2rem] border border-amber-200/80 bg-white/85 p-8 shadow-2xl shadow-amber-100/50 backdrop-blur dark:border-yellow-400/20 dark:bg-white/5 dark:shadow-black/30">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="inline-flex rounded-full border border-amber-300/70 bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-amber-800 dark:border-yellow-300/30 dark:bg-yellow-400/10 dark:text-yellow-200">
                            Globio Premium
                        </div>
                        @if ($hasPremium)
                            <x-premium-badge label="Badge abbonato attivo" size="md" variant="solid" />
                        @endif
                    </div>
                    <h1 class="mt-5 max-w-2xl text-4xl font-black tracking-tight text-slate-950 dark:text-white sm:text-5xl">
                        {{ $subscriptionState['title'] === 'Premium attivo' ? 'Il tuo premium e attivo e sempre sotto controllo.' : 'Video e reels senza pubblicita, con controlli e funzioni in piu.' }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600 dark:text-gray-300 sm:text-lg">
                        {{ $subscriptionState['message'] }}
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-black/20">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Senza pubblicita</p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Banner e video ads nascosti per gli utenti premium.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-black/20">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Riproduzione avanzata</p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Background playback, picture in picture e controlli premium.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-black/20">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Qualita superiore</p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Streaming avanzato per i video e una migliore esperienza continua.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white/80 p-4 dark:border-white/10 dark:bg-black/20">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Reels migliori</p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-gray-300">Controlli extra, queue management e smart downloads lato app.</p>
                        </div>
                    </div>
                </div>

                <aside class="rounded-[2rem] border border-slate-200 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 dark:border-white/10 dark:bg-slate-900/90 dark:shadow-black/30">
                    @if ($errors->any())
                        <div class="mb-5 rounded-2xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-700 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-100">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="mb-5 rounded-2xl border border-emerald-300 bg-emerald-50 p-4 text-sm text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-100">
                            {{ session('success') }}
                        </div>
                    @endif

                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-700 dark:text-sky-200">Piano Mensile</p>
                    <div class="mt-4 flex items-end gap-3">
                        <span class="text-5xl font-black text-slate-950 dark:text-white">{{ $plan['formatted_price'] }}</span>
                        <span class="pb-2 text-sm text-slate-500 dark:text-gray-400">/ mese</span>
                    </div>

                    <ul class="mt-6 space-y-3 text-sm text-slate-700 dark:text-gray-200">
                        <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-yellow-300"></span><span>Nessuna pubblicita durante la navigazione premium.</span></li>
                        <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-yellow-300"></span><span>Funzioni extra per video e reels.</span></li>
                        <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-yellow-300"></span><span>Checkout e rinnovi sicuri con Stripe.</span></li>
                        <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-yellow-300"></span><span>Gestione del piano dal customer portal.</span></li>
                    </ul>

                    <div class="mt-8">
                        @auth
                            @if ($hasPremium)
                                <div class="rounded-2xl border {{ $activeSubscription?->cancel_at_period_end ? 'border-amber-400/20 bg-amber-400/10 text-amber-100' : 'border-emerald-400/20 bg-emerald-400/10 text-emerald-100' }} p-4 text-sm">
                                    <div class="font-semibold">{{ $subscriptionState['title'] }}</div>
                                    @if ($activeSubscription?->current_period_end)
                                        <div class="mt-2 {{ $activeSubscription?->cancel_at_period_end ? 'text-amber-200/90' : 'text-emerald-200/90' }}">
                                            Accesso valido fino al {{ $activeSubscription->current_period_end->format('d/m/Y') }}.
                                        </div>
                                    @endif
                                    <div class="mt-2 {{ $activeSubscription?->cancel_at_period_end ? 'text-amber-200/90' : 'text-emerald-200/90' }}">
                                        Stato rinnovo: {{ $activeSubscription?->cancel_at_period_end ? 'disdetto a fine periodo' : 'attivo' }}.
                                    </div>
                                </div>

                                <div class="mt-4 space-y-3">
                                    @if ($activeSubscription?->cancel_at_period_end)
                                        <form method="POST" action="{{ route('premium.resume') }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-2xl bg-amber-300 px-5 py-3 text-sm font-semibold text-gray-950 transition hover:bg-amber-200">
                                                Riattiva rinnovo
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('premium.cancel') }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-2xl bg-rose-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-400">
                                                Disdici quando vuoi
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('premium.portal') }}">
                                        @csrf
                                        <button type="submit" class="w-full rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-gray-950 transition hover:bg-yellow-100">
                                            Gestisci abbonamento
                                        </button>
                                    </form>
                                </div>
                            @elseif ($isConfigured)
                                <form method="POST" action="{{ route('premium.checkout') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-2xl bg-yellow-400 px-5 py-3 text-sm font-bold text-gray-950 transition hover:bg-yellow-300">
                                        Abbonati ora
                                    </button>
                                </form>
                            @else
                                <div class="rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-100">
                                    L'abbonamento premium non e ancora configurato dall'amministrazione.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full rounded-2xl bg-yellow-400 px-5 py-3 text-center text-sm font-bold text-gray-950 transition hover:bg-yellow-300">
                                Accedi per abbonarti
                            </a>
                        @endauth

                        <p class="mt-4 text-xs leading-6 text-slate-500 dark:text-gray-400">
                            Rinnovo mensile automatico. Puoi disdire o riattivare il rinnovo in qualsiasi momento, mantenendo l'accesso fino alla fine del periodo gia pagato.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</x-layout>

<x-layout>
    <section class="relative min-h-[calc(100vh-8rem)] overflow-hidden bg-gray-950">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.2),transparent_26%),radial-gradient(circle_at_bottom_left,rgba(250,204,21,0.18),transparent_30%)]"></div>

        <div class="relative mx-auto flex min-h-[calc(100vh-8rem)] max-w-5xl items-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full rounded-[2rem] border border-emerald-400/20 bg-white/5 p-8 shadow-2xl shadow-black/30 backdrop-blur md:p-10">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-300/30 bg-emerald-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-200">
                            <i class="fas fa-circle-check"></i>
                            <span>Pagamento riuscito</span>
                        </div>
                        <h1 class="mt-5 text-4xl font-black tracking-tight text-white sm:text-5xl">
                            Benvenuto in Globio Premium.
                        </h1>
                        <p class="mt-5 text-base leading-7 text-gray-300 sm:text-lg">
                            Il checkout e stato completato con successo. Ora puoi tornare alla sezione premium per vedere lo stato aggiornato del tuo abbonamento e il tuo badge da abbonato.
                        </p>

                        @if ($synced)
                            <div class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-sm text-emerald-100">
                                Abbonamento sincronizzato correttamente con Globio.
                                @if ($activeSubscription?->current_period_end)
                                    <div class="mt-2 text-emerald-200/90">
                                        Accesso premium valido fino al {{ $activeSubscription->current_period_end->format('d/m/Y') }}.
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if ($syncError)
                            <div class="mt-6 rounded-2xl border border-amber-400/20 bg-amber-400/10 p-4 text-sm text-amber-100">
                                {{ $syncError }}
                                @if ($sessionId)
                                    <div class="mt-2 break-all font-mono text-xs text-amber-200/90">{{ $sessionId }}</div>
                                @endif
                            </div>
                        @endif

                        @if ($sessionId)
                            <div class="mt-4 rounded-2xl border border-white/10 bg-black/20 p-4 text-sm text-gray-300">
                                Sessione checkout: <span class="break-all font-mono text-gray-100">{{ $sessionId }}</span>
                            </div>
                        @endif

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('premium.index') }}"
                                class="inline-flex items-center justify-center rounded-2xl bg-emerald-400 px-6 py-3 text-sm font-bold text-gray-950 transition hover:bg-emerald-300">
                                Vai a Premium
                            </a>
                            <a href="{{ route('home') }}"
                                class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                Torna alla home
                            </a>
                        </div>
                    </div>

                    <div class="grid gap-4 rounded-[1.75rem] border border-white/10 bg-black/20 p-6 lg:min-w-[320px]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-400 text-gray-950">
                                <i class="fas fa-crown text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-white">Premium attivabile subito</p>
                                <p class="text-sm text-gray-400">Navigazione senza pubblicita e funzioni extra</p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-gray-300">
                            Questa pagina ora prova a sincronizzare subito lo stato premium usando il `session_id` restituito da Stripe, senza dipendere solo dal webhook.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>

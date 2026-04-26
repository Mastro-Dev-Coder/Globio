<x-layout>
    <section class="relative min-h-[calc(100vh-8rem)] overflow-hidden bg-gray-950">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(59,130,246,0.18),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(250,204,21,0.14),transparent_30%)]"></div>

        <div class="relative mx-auto flex min-h-[calc(100vh-8rem)] max-w-4xl items-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full rounded-[2rem] border border-sky-400/20 bg-white/5 p-8 text-center shadow-2xl shadow-black/30 backdrop-blur md:p-10">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-sky-400/15 text-sky-200">
                    <i class="fas fa-arrow-rotate-left text-3xl"></i>
                </div>
                <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-sky-300/30 bg-sky-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-sky-200">
                    <span>Gestione abbonamento</span>
                </div>
                <h1 class="mt-5 text-4xl font-black tracking-tight text-white sm:text-5xl">
                    Portale abbonamento chiuso.
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-gray-300 sm:text-lg">
                    Hai terminato la gestione del tuo piano. Puoi rientrare nella pagina premium per verificare subito stato, rinnovo e badge abbonato.
                </p>

                @if ($activeSubscription?->current_period_end)
                    <div class="mx-auto mt-6 max-w-2xl rounded-2xl border {{ $activeSubscription->cancel_at_period_end ? 'border-amber-400/20 bg-amber-400/10 text-amber-100' : 'border-emerald-400/20 bg-emerald-400/10 text-emerald-100' }} p-4 text-sm">
                        <div>
                            Stato attuale: {{ $activeSubscription->cancel_at_period_end ? 'premium attivo fino a fine periodo' : 'premium attivo con rinnovo automatico' }}.
                        </div>
                        <div class="mt-2">
                            Accesso valido fino al {{ $activeSubscription->current_period_end->format('d/m/Y') }}.
                        </div>
                    </div>
                @endif

                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ route('premium.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-bold text-gray-950 transition hover:bg-sky-100">
                        Torna a Premium
                    </a>
                    <a href="{{ route('users.profile') }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Vai al profilo
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layout>

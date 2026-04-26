<x-layout>
    <section class="relative min-h-[calc(100vh-8rem)] overflow-hidden bg-gray-950">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(244,63,94,0.18),transparent_26%),radial-gradient(circle_at_bottom_left,rgba(59,130,246,0.16),transparent_30%)]"></div>

        <div class="relative mx-auto flex min-h-[calc(100vh-8rem)] max-w-5xl items-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full rounded-[2rem] border border-rose-400/20 bg-white/5 p-8 shadow-2xl shadow-black/30 backdrop-blur md:p-10">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-rose-300/30 bg-rose-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-rose-200">
                            <i class="fas fa-circle-xmark"></i>
                            <span>Pagamento non completato</span>
                        </div>
                        <h1 class="mt-5 text-4xl font-black tracking-tight text-white sm:text-5xl">
                            Nessun addebito completato.
                        </h1>
                        <p class="mt-5 text-base leading-7 text-gray-300 sm:text-lg">
                            Il checkout premium e stato annullato o interrotto prima della conferma finale. Nessun problema: puoi riprovare quando vuoi.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('premium.index') }}"
                                class="inline-flex items-center justify-center rounded-2xl bg-yellow-400 px-6 py-3 text-sm font-bold text-gray-950 transition hover:bg-yellow-300">
                                Riprova con Premium
                            </a>
                            <a href="{{ route('home') }}"
                                class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                Torna alla home
                            </a>
                        </div>
                    </div>

                    <div class="grid gap-4 rounded-[1.75rem] border border-white/10 bg-black/20 p-6 lg:min-w-[320px]">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-gray-300">
                            Se hai chiuso Stripe per errore, puoi rientrare subito nella pagina premium e riprendere l'attivazione.
                        </div>
                        <div class="rounded-2xl border border-amber-400/20 bg-amber-400/10 p-4 text-sm text-amber-100">
                            Il badge premium e le funzioni avanzate vengono attivati solo dopo il completamento del checkout.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>

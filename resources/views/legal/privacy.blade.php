<x-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-6 py-8">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $page->title ?? 'Privacy Policy' }}</h1>
                        <p class="text-gray-600 dark:text-gray-400">Ultima modifica: {{ $page->updated_at ? $page->updated_at->format('d F Y') : '27 novembre 2025' }}</p>
                    </div>

                    <div
                        class="prose prose-gray dark:prose-invert max-w-none space-y-6 text-gray-700 dark:text-gray-300">
                        @if($page && $page->content)
                            {!! $page->content !!}
                        @else

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">1. Introduzione</h2>
                                <p>La presente Privacy Policy descrive come Globio ("noi", "nostro", "la piattaforma")
                                    raccoglie, utilizza e protegge le informazioni quando utilizzi il nostro servizio di
                                    video streaming.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">2. Informazioni che
                                    Raccogliamo</h2>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">2.1 Informazioni Fornite
                                    Direttamente</h3>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Dati di registrazione (nome, email, username)</li>
                                    <li>Informazioni del profilo (biografia, avatar, banner)</li>
                                    <li>Contenuti caricati (video, miniature, descrizioni)</li>
                                    <li>Messaggi e commenti</li>
                                </ul>

                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 mt-4">2.2 Informazioni
                                    Raccoglie Automaticamente</h3>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Indirizzo IP e informazioni del dispositivo</li>
                                    <li>Dati di navigazione e interazione</li>
                                    <li>Cookie e tecnologie simili</li>
                                    <li>Dati di utilizzo della piattaforma</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">3. Come Utilizziamo le
                                    Informazioni</h2>
                                <p>Utilizziamo le informazioni raccolte per:</p>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Fornire e migliorare i nostri servizi</li>
                                    <li>Gestire il tuo account utente</li>
                                    <li>Personalizzare la tua esperienza</li>
                                    <li>Comunicare contigo riguardo a servizi e aggiornamenti</li>
                                    <li>Garantire la sicurezza e prevenire abusi</li>
                                    <li>Rispettare obblighi legali</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">4. Condivisione delle
                                    Informazioni</h2>
                                <p>Non vendiamo le tue informazioni personali. Possiamo condividere informazioni solo nei
                                    seguenti casi:</p>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Con il tuo consenso</li>
                                    <li>Per fornire i nostri servizi</li>
                                    <li>Per rispettare obblighi legali</li>
                                    <li>Per proteggere i nostri diritti e sicurezza</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">5. Sicurezza dei Dati
                                </h2>
                                <p>Adottiamo misure tecniche e organizzative appropriate per proteggere le tue informazioni
                                    contro accesso non autorizzato, alterazione, divulgazione o distruzione. Tuttavia,
                                    nessun sistema è completamente sicuro.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">6. I Tuoi Diritti</h2>
                                <p>Hai il diritto di:</p>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Accedere alle tue informazioni personali</li>
                                    <li>Correggere informazioni inaccurate</li>
                                    <li>Cancellare i tuoi dati (diritto all'oblio)</li>
                                    <li>Limitare il trattamento</li>
                                    <li>Portabilità dei dati</li>
                                    <li>Opporti al trattamento</li>
                                </ul>
                                <p class="mt-2">Per esercitare questi diritti, contattaci a <a
                                        href="mailto:privacy@globio.com"
                                        class="text-red-600 hover:underline">privacy@globio.com</a></p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">7. Cookie</h2>
                                <p>Utilizziamo cookie per migliorare la tua esperienza. Puoi gestire le preferenze dei
                                    cookie nelle impostazioni del tuo browser. La disabilitazione dei cookie potrebbe
                                    limitare alcune funzionalità del servizio.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">8. Conservazione dei
                                    Dati</h2>
                                <p>Conserviamo le tue informazioni per tutto il tempo necessario a fornire i servizi e per
                                    rispettare obblighi legali. Quando cancelli il tuo account, elimineremo le tue
                                    informazioni entro 30 giorni, salvo diverse disposizioni legali.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">9. Modifiche alla
                                    Privacy Policy</h2>
                                <p>Potremmo aggiornare questa Privacy Policy periodicamente. Ti notificheremo eventuali
                                    modifiche pubblicando la nuova policy sul sito e aggiornando la data di "ultima
                                    modifica".</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">10. Contatti</h2>
                                <p>Per domande sulla presente Privacy Policy, contattaci:</p>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Email: <a href="mailto:privacy@globio.com"
                                            class="text-red-600 hover:underline">privacy@globio.com</a></li>
                                    <li>Indirizzo: Roma, Italia</li>
                                </ul>
                            </section>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

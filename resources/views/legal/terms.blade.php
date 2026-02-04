<x-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-6 py-8">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $page->title ?? 'Termini di Servizio' }}</h1>
                        <p class="text-gray-600 dark:text-gray-400">Ultima modifica: {{ $page->updated_at ? $page->updated_at->format('d F Y') : '27 novembre 2025' }}</p>
                    </div>

                    <div
                        class="prose prose-gray dark:prose-invert max-w-none space-y-6 text-gray-700 dark:text-gray-300">
                        @if($page && $page->content)
                            {!! $page->content !!}
                        @else

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">1. Accettazione dei
                                    Termini</h2>
                                <p>Utilizzando Globio, accetti di essere vincolato da questi Termini di Servizio. Se non
                                    accetti questi termini, non utilizzare il nostro servizio.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">2. Descrizione del
                                    Servizio</h2>
                                <p>Globio è una piattaforma di video streaming che permette agli utenti di caricare,
                                    condividere e visualizzare contenuti video. Il servizio include funzionalità di
                                    registrazione, gestione profili, commenti e interazioni sociali.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">3. Registrazione
                                    dell'Account</h2>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">3.1 Requisiti</h3>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Devi avere almeno 13 anni per creare un account</li>
                                    <li>Le informazioni di registrazione devono essere accurate e aggiornate</li>
                                    <li>Sei responsabile della sicurezza del tuo account</li>
                                </ul>

                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 mt-4">3.2 Sicurezza
                                    dell'Account</h3>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Non condividere le credenziali del tuo account</li>
                                    <li>Notifica immediatamente qualsiasi uso non autorizzato</li>
                                    <li>Utilizza password sicure e uniche</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">4. Contenuti degli
                                    Utenti</h2>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">4.1 Responsabilità</h3>
                                <p>Sei l'unico responsabile dei contenuti che carichi sulla piattaforma. Garantisci di avere
                                    tutti i diritti necessari per condividere tali contenuti.</p>

                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 mt-4">4.2 Contenuti
                                    Vietati</h3>
                                <p>È vietato caricare contenuti che:</p>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Violino diritti d'autore o altri diritti di proprietà intellettuale</li>
                                    <li>Siano illegali, offensivi, diffamatori, invasivi della privacy</li>
                                    <li>Contengano materiale pornografico o sessualmente esplicito</li>
                                    <li>Promuovano violenza, odio o discriminazione</li>
                                    <li>Contengano malware o codici dannosi</li>
                                </ul>

                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 mt-4">4.3 Modifica e
                                    Rimozione</h3>
                                <p>Ci riserviamo il diritto di modificare, rimuovere o rifiutare qualsiasi contenuto che
                                    violi questi termini o sia ritenuto inappropriato, senza preavviso.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">5. Diritti di Proprietà
                                    Intellettuale</h2>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">5.1 I Tuoi Diritti</h3>
                                <p>Mantieni tutti i diritti sui contenuti che carichi. Con il caricamento, concedi a Globio
                                    una licenza non esclusiva, mondiale, gratuita per utilizzare, modificare e distribuire i
                                    tuoi contenuti per fornire e migliorare il servizio.</p>

                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 mt-4">5.2 Diritti di
                                    Globio</h3>
                                <p>Tutti i diritti sulla piattaforma Globio, inclusi design, software e loghi, rimangono di
                                    nostra proprietà. Non ti è permesso utilizzare i nostri marchi senza autorizzazione.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">6. Condotta degli
                                    Utenti</h2>
                                <p>Ti impegni a:</p>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Utilizzare il servizio in modo legale e appropriato</li>
                                    <li>Rispetto degli altri utenti</li>
                                    <li>Non tentare di danneggiare o interferire con il servizio</li>
                                    <li>Non utilizzare bot o metodi automatizzati non autorizzati</li>
                                    <li>Non violare la privacy di altri utenti</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">7. Privacy</h2>
                                <p>La tua privacy è importante per noi. Consulta la nostra <a href="{{ route('privacy') }}"
                                        class="text-red-600 hover:underline">Privacy Policy</a> per informazioni dettagliate
                                    su come raccogliamo e utilizziamo i tuoi dati.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">8. Limitazioni di
                                    Responsabilità</h2>
                                <p>Globio è fornito "così com'è" senza garanzie di alcun tipo. Non siamo responsabili per:
                                </p>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Danni diretti, indiretti o consequenziali</li>
                                    <li>Contenuti caricati da utenti terzi</li>
                                    <li>Interruzioni del servizio</li>
                                    <li>Perdita di dati o contenuti</li>
                                </ul>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">9. Risoluzione del
                                    Servizio</h2>
                                <p>Possiamo sospendere o terminare il tuo account in caso di violazione di questi termini.
                                    In tal caso, perderai l'accesso al tuo account e ai tuoi contenuti.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">10. Modifiche ai
                                    Termini</h2>
                                <p>Ci riserviamo il diritto di modificare questi termini in qualsiasi momento. Gli utenti
                                    saranno notificati delle modifiche significative. L'uso continuato del servizio
                                    costituisce accettazione dei termini modificati.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">11. Legge Applicabile
                                </h2>
                                <p>Questi termini sono regolati dalle leggi italiane. Eventuali dispute saranno risolte nei
                                    tribunali di Roma, Italia.</p>
                            </section>

                            <section>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">12. Contatti</h2>
                                <p>Per domande sui Termini di Servizio, contattaci:</p>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>Email: <a href="mailto:legal@globio.com"
                                            class="text-red-600 hover:underline">legal@globio.com</a></li>
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

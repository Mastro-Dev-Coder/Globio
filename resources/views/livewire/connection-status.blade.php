<div>
    @if ($showOfflinePage)
        <div class="fixed inset-0 bg-white dark:bg-gray-900 flex items-center justify-center z-[9999] offline-overlay">
            <div class="text-center max-w-md px-4">
                <div class="mb-8">
                    <i class="fas fa-wifi-slash text-8xl mb-6 text-gray-400"></i>
                </div>

                <h1 class="text-4xl font-bold mb-4 text-gray-900 dark:text-white">
                    Nessuna connessione
                </h1>

                <p class="text-lg mb-8 text-gray-600 dark:text-gray-300">
                    Controlla la tua connessione internet e riprova.
                </p>

                <div class="space-y-4">
                    <button onclick="window.location.reload()"
                        class="w-full px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-lg cursor-pointer">
                        Riprova
                    </button>
                    
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Oppure attendi che la connessione venga ripristinata automaticamente.
                    </div>
                </div>
            </div>
        </div>
    @elseif ($showSlowPage)
        <div class="fixed bottom-4 right-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg shadow-lg p-4 z-[9999] max-w-sm slow-connection-banner">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                <div>
                    <p class="font-medium text-yellow-800 dark:text-yellow-200">
                        Connessione lenta
                    </p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        Potrebbero verificarsi ritardi nel caricamento.
                    </p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                    class="ml-auto text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attendi che Livewire sia pronto
        if (typeof Livewire === 'undefined') {
            console.warn('Livewire non ancora caricato, ritardo inizializzazione');
            document.addEventListener('livewire:initialized', initConnectionChecker);
            return;
        }
        
        initConnectionChecker();
    });

    function initConnectionChecker() {
        let currentStatus = 'online';
        let slowCounter = 0;
        let isChecking = false;
        let retryCount = 0;
        const MAX_RETRIES = 3;
        const SLOW_THRESHOLD = 1500; // ms
        const CHECK_INTERVAL = 10000; // 10 secondi

        function showToast(message, type = 'info') {
            // Usa la funzione toast globale se disponibile
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
                return;
            }

            const colors = {
                error: 'bg-red-600',
                warning: 'bg-yellow-600',
                success: 'bg-green-600',
                info: 'bg-blue-600'
            };

            const toast = document.createElement('div');
            toast.className =
                `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-[9999] text-white transition-opacity duration-300 ${colors[type]}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        function updateStatus(newStatus) {
            if (currentStatus === newStatus) return;

            const previous = currentStatus;
            currentStatus = newStatus;

            // Gestisci classe body per bloccare interazioni
            document.body.classList.remove('offline-mode', 'slow-mode');
            if (newStatus === 'offline') {
                document.body.classList.add('offline-mode');
            } else if (newStatus === 'slow') {
                document.body.classList.add('slow-mode');
            }

            // Usa dispatch per Livewire 3
            try {
                Livewire.dispatch('connectionStatusChanged', { status: newStatus });
            } catch (e) {
                // Fallback per versioni precedenti
                try {
                    Livewire.emit('connectionStatusChanged', newStatus);
                } catch (e2) {
                    console.warn('Impossibile inviare evento connectionStatusChanged');
                }
            }

            // Mostra notifiche
            if (newStatus === 'offline') {
                showToast('Nessuna connessione internet', 'error');
            } else if (newStatus === 'slow') {
                showToast('Connessione lenta rilevata', 'warning');
            } else if (newStatus === 'online' && previous !== 'online') {
                showToast('Connessione ripristinata', 'success');
                retryCount = 0; // Reset retry counter
            }
        }

        async function checkConnection() {
            if (isChecking) return;
            isChecking = true;

            try {
                // Prima verifica se siamo offline
                if (!navigator.onLine) {
                    updateStatus('offline');
                    isChecking = false;
                    return;
                }

                const start = performance.now();

                // Usa una cache-buster per evitare cache del browser
                const response = await fetch('/favicon.ico?t=' + Date.now(), {
                    method: 'HEAD',
                    cache: 'no-store',
                    mode: 'no-cors'
                });

                const latency = performance.now() - start;

                if (latency > SLOW_THRESHOLD) {
                    slowCounter++;
                    retryCount++;
                } else {
                    slowCounter = 0;
                }

                // Determina stato basato su counter
                if (slowCounter >= 2 && retryCount < MAX_RETRIES) {
                    updateStatus('slow');
                } else if (slowCounter >= MAX_RETRIES) {
                    updateStatus('offline');
                } else {
                    updateStatus('online');
                }

            } catch (error) {
                slowCounter++;
                retryCount++;
                
                if (slowCounter >= 2) {
                    updateStatus('offline');
                } else {
                    // Ritenta dopo errore singolo
                    setTimeout(checkConnection, 2000);
                }
            }

            isChecking = false;
        }

        // Eventi browser per online/offline
        window.addEventListener('online', function() {
            retryCount = 0;
            checkConnection();
        });
        
        window.addEventListener('offline', function() {
            updateStatus('offline');
        });

        // Blocca navigazione se offline
        document.addEventListener('click', function(e) {
            if (!navigator.onLine) {
                const link = e.target.closest('a');
                const form = e.target.closest('form');
                const button = e.target.closest('button[type="submit"], input[type="submit"], .btn-submit');

                if (link || form || button) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    showToast('Nessuna connessione - impossibile completare l\'azione', 'error');
                    return false;
                }
            }
        }, true);

        document.addEventListener('submit', function(e) {
            if (!navigator.onLine) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showToast('Nessuna connessione - impossibile inviare il modulo', 'error');
                return false;
            }
        }, true);

        // Gestisci navigazione history
        window.addEventListener('popstate', function() {
            if (!navigator.onLine) {
                updateStatus('offline');
            }
        });

        // Blocca refresh manuale se offline
        window.addEventListener('keydown', function(e) {
            if (!navigator.onLine && (e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                showToast('Refresh non disponibile offline', 'warning');
            }
        });

        // Gestisci richieste fetch globali
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            if (!navigator.onLine) {
                showToast('Nessuna connessione - richiesta non completata', 'error');
                return Promise.reject(new Error('Offline'));
            }
            return originalFetch.apply(this, args);
        };

        // Avvia check iniziale
        setTimeout(checkConnection, 1000);
        
        // Check periodico
        setInterval(checkConnection, CHECK_INTERVAL);
    }
</script>

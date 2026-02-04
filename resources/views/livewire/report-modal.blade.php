<div>
    @if ($showModal)
        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-gray-900/70 bg-opacity-50 z-50 flex items-center justify-center p-4"
            wire:key="report-modal-backdrop">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden"
                wire:key="report-modal-content">

                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-flag text-red-500"></i>
                        Segnala
                        {{ $targetType === 'video' ? 'video' : ($targetType === 'channel' ? 'canale' : 'commento') }}
                    </h3>
                    <button wire:click="closeModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto max-h-[calc(90vh-140px)]">
                    <div class="p-6">
                        <!-- Target Info -->
                        @if ($targetTitle)
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
                                <div class="flex items-center gap-3">
                                    @if ($targetType === 'video')
                                        <i class="fas fa-video text-red-500 text-xl"></i>
                                    @elseif($targetType === 'channel')
                                        <i class="fas fa-tv text-purple-500 text-xl"></i>
                                    @else
                                        <i class="fas fa-comment text-blue-500 text-xl"></i>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $targetTitle }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Stai per segnalare questo
                                            {{ $targetType === 'video' ? 'video' : ($targetType === 'channel' ? 'canale' : 'commento') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Form -->
                        <form wire:submit.prevent="submitReport">
                            @csrf

                            <!-- Reason Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="fas fa-list-ul text-primary mr-2"></i>
                                    Perché stai segnalando questo contenuto?
                                </label>

                                <div class="grid grid-cols-1 gap-2">
                                    @foreach ($targetReasons as $index => $reason)
                                        <div class="relative">
                                            <input type="radio" wire:model="reportType" class="peer sr-only"
                                                id="reason_{{ $reason['value'] }}" value="{{ $reason['value'] }}">
                                            <label for="reason_{{ $reason['value'] }}"
                                                class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20">
                                                <i
                                                    class="fas {{ $reason['icon'] }} text-gray-400 peer-checked:text-red-500"></i>
                                                <span
                                                    class="text-sm text-gray-700 dark:text-gray-300">{{ $reason['label'] }}</span>
                                            </label>
                                        </div>
                                    @endforeach

                                    <!-- Custom reason option -->
                                    <div class="relative">
                                        <input type="radio" wire:model="reportType" class="peer sr-only"
                                            id="reason_custom" value="custom">
                                        <label for="reason_custom"
                                            class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20">
                                            <i class="fas fa-pen text-gray-400 peer-checked:text-red-500"></i>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Altro
                                                (specifica)</span>
                                        </label>
                                    </div>
                                </div>

                                @error('reportType')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Custom Reason Input -->
                            <div class="mb-6" x-show="$wire.reportType === 'custom'" x-transition>
                                <label for="customReason"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-pen text-primary mr-2"></i>
                                    Specifica il motivo
                                </label>
                                <input type="text" wire:model="customReason" id="customReason"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                    placeholder="Descrivi brevemente il motivo">
                                @error('customReason')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-align-left text-primary mr-2"></i>
                                    Descrizione aggiuntiva (opzionale)
                                </label>
                                <textarea wire:model="description" id="description" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                                    placeholder="Fornisci maggiori dettagli..."></textarea>
                                <div class="text-right mt-1">
                                    <small class="text-gray-500">{{ strlen($description) }}/1000</small>
                                </div>
                                @error('description')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Info Box -->
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                                <h6 class="text-yellow-800 dark:text-yellow-400 font-medium mb-2">
                                    <i class="fas fa-info-circle mr-2"></i>Come gestiamo le segnalazioni
                                </h6>
                                <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                                    <li>• Il nostro team esaminerà la segnalazione entro 24-48 ore</li>
                                    <li>• Se il contenuto viola le linee guida, verrà rimosso</li>
                                    <li>• Le segnalazioni false possono comportare azioni sul tuo account</li>
                                </ul>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3">
                                <button type="button" wire:click="closeModal()"
                                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Annulla
                                </button>
                                <button type="submit"
                                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center gap-2">
                                    <i class="fas fa-flag"></i>
                                    Invia Segnalazione
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

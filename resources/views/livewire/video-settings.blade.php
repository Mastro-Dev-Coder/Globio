<div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('ui.video_settings') }}</h3>
            <div class="text-sm text-gray-500">
                {{ __('ui.last_modified') }} {{ $video->updated_at->diffForHumans() }}
            </div>
        </div>

        @if(!$canEdit)
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm text-yellow-700">
                        {{ __('ui.only_owner_edit') }}
                    </p>
                </div>
            </div>
        @endif

        <form wire:submit="updateSettings" class="space-y-6">
            <!-- Abilitazione Commenti -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-base font-medium text-gray-900">{{ __('ui.comments_settings') }}</h4>
                        <p class="text-sm text-gray-500">{{ __('ui.allow_comments_desc') }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               class="sr-only peer" 
                               wire:model.live="commentsEnabled"
                               {{ !$canEdit ? 'disabled' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                @if($commentsEnabled)
                    <div class="ml-4 pl-4 border-l-2 border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-sm font-medium text-gray-900">{{ __('ui.require_approval') }}</h5>
                                <p class="text-xs text-gray-500">{{ __('ui.approval_required_desc') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       class="sr-only peer" 
                                       wire:model.live="commentsRequireApproval"
                                       {{ !$canEdit ? 'disabled' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Abilitazione Like/Dislike -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-medium text-gray-900">{{ __('ui.likes_dislikes_settings') }}</h4>
                        <p class="text-sm text-gray-500">{{ __('ui.allow_likes_desc') }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               class="sr-only peer" 
                               wire:model.live="likesEnabled"
                               {{ !$canEdit ? 'disabled' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <!-- Configurazioni Rapide -->
            @if($canEdit)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('ui.quick_config') }}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <button type="button" 
                                wire:click="enableAllWithoutApproval"
                                class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('ui.enable_all') }}
                        </button>

                        <button type="button" 
                                wire:click="enableCommentsWithApproval"
                                class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            {{ __('ui.comments_approval') }}
                        </button>

                        <button type="button" 
                                wire:click="disableAllInteractions"
                                class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636"></path>
                            </svg>
                            {{ __('ui.disable_all') }}
                        </button>
                    </div>
                </div>
            @endif

            <!-- Pulsanti di Azione -->
            @if($canEdit)
                <div class="flex items-center justify-between pt-6">
                    <button type="button" 
                            wire:click="resetToDefaults"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ __('ui.reset_defaults') }}
                    </button>

                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <svg wire:loading class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>{{ __('ui.save_settings') }}</span>
                        <span wire:loading>{{ __('ui.saving') }}</span>
                    </button>
                </div>
            @endif
        </form>

        <!-- Status Correnti -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('ui.current_status') }}</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($commentsEnabled)
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                    <span class="ml-2">
                        <span class="{{ $commentsEnabled ? 'text-green-600' : 'text-red-600' }}">{{ $commentsEnabled ? __('ui.comments_enabled') : __('ui.comments_disabled_status') }}</span>
                    </span>
                </div>

                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($likesEnabled)
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                    <span class="ml-2">
                        <span class="{{ $likesEnabled ? 'text-green-600' : 'text-red-600' }}">{{ $likesEnabled ? __('ui.likes_enabled') : __('ui.likes_disabled') }}</span>
                    </span>
                </div>

                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($commentsRequireApproval)
                            <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                    <span class="ml-2">
                        <span class="{{ $commentsRequireApproval ? 'text-orange-600' : 'text-green-600' }}">{{ $commentsRequireApproval ? __('ui.approval_required_status') : __('ui.approval_not_required') }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Messaggi di Feedback -->
        <div x-data="{ show: @entangle('successMessage') }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="fixed top-4 right-4 z-50"
             style="display: none;">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ __('ui.settings_updated_success') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

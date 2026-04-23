<div class="relative" x-data="{ showDropdown: @entangle('showDropdown') }">
    <!-- Language Button -->
    <button @click="showDropdown = !showDropdown"
        class="flex items-center gap-2 px-3 py-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        aria-label="{{ __('ui.language') }}">
        <!-- Globe Icon -->
        <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
            </path>
        </svg>

        <!-- Current Language Flag/Code -->
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:inline">
            {{ strtoupper(app()->getLocale()) }}
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="showDropdown" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95" @click.away="showDropdown = false"
        class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50"
        style="display: none;">
        @php
            $currentLocale = app()->getLocale();
            $languages = [
                'it' => ['name' => __('ui.language_italian'), 'flag' => '🇮🇹'],
                'en' => ['name' => __('ui.language_english'), 'flag' => '🇬🇧'],
                'es' => ['name' => __('ui.language_spanish'), 'flag' => '🇪🇸'],
            ];
        @endphp

        <!-- Language Options -->
        @foreach ($languages as $code => $lang)
            <button wire:click="switchLocale('{{ $code }}')"
                class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors {{ $currentLocale === $code ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                <span class="text-lg">{{ $lang['flag'] }}</span>
                <span class="font-medium">{{ $lang['name'] }}</span>
                @if ($currentLocale === $code)
                    <svg class="w-4 h-4 ml-auto text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                @endif
            </button>
        @endforeach
    </div>
</div>

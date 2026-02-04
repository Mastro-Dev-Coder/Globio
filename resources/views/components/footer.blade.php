<div class="px-4 text-[11px] text-gray-600 dark:text-gray-400 space-y-3 select-none">
    <!-- Menu Principale -->
    <div class="flex flex-wrap gap-x-6 gap-y-2">
        <a href="{{ route('contact') }}" class="hover:underline">Contatti</a>
        <a href="{{ route('privacy') }}" class="hover:underline">Privacy Policy</a>
        <a href="{{ route('terms') }}" class="hover:underline">Termini di Servizio</a>
    </div>

    <!-- Copyright -->
    <div class="pt-2 text-[10px] text-gray-500 dark:text-gray-500 border-t border-gray-200 dark:border-gray-700">
        © {{ date('Y') }} {{ \App\Models\Setting::getValue('site_name', 'Globio') }} - Tutti i diritti riservati
    </div>
</div>

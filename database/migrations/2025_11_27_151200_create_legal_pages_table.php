<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('legal_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // 'contatti', 'privacy', 'termini'
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
        });
        
        // Inserisco i contenuti di default
        DB::table('legal_pages')->insert([
            [
                'slug' => 'contatti',
                'title' => 'Contatti',
                'content' => '<div class="mb-8 text-center">
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Informazioni di Contatto</h2>
                                <div class="grid md:grid-cols-3 gap-6 mt-6">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 text-red-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Email</h3>
                                        <p class="text-gray-600 dark:text-gray-400">info@globio.com</p>
                                    </div>
                                    <div class="text-center">
                                        <svg class="w-8 h-8 text-red-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Indirizzo</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Roma, Italia</p>
                                    </div>
                                    <div class="text-center">
                                        <svg class="w-8 h-8 text-red-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Orari</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Lun-Ven 9:00 - 18:00</p>
                                    </div>
                                </div>
                              </div>',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'content' => '<section class="mb-8">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">1. Introduzione</h2>
                                <p class="text-gray-700 dark:text-gray-300">La presente Privacy Policy descrive come Globio raccoglie, utilizza e protegge le informazioni quando utilizzi il nostro servizio di video streaming.</p>
                              </section>
                              <section class="mb-8">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">2. Informazioni che Raccogliamo</h2>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">2.1 Informazioni Fornite Direttamente</h3>
                                <ul class="list-disc pl-6 space-y-1 text-gray-700 dark:text-gray-300">
                                    <li>Dati di registrazione (nome, email, username)</li>
                                    <li>Informazioni del profilo (biografia, avatar, banner)</li>
                                    <li>Contenuti caricati (video, miniature, descrizioni)</li>
                                    <li>Messaggi e commenti</li>
                                </ul>
                              </section>',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'slug' => 'termini',
                'title' => 'Termini di Servizio',
                'content' => '<section class="mb-8">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">1. Accettazione dei Termini</h2>
                                <p class="text-gray-700 dark:text-gray-300">Utilizzando Globio, accetti di essere vincolato da questi Termini di Servizio. Se non accetti questi termini, non utilizzare il nostro servizio.</p>
                              </section>
                              <section class="mb-8">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">2. Descrizione del Servizio</h2>
                                <p class="text-gray-700 dark:text-gray-300">Globio è una piattaforma di video streaming che permette agli utenti di caricare, condividere e visualizzare contenuti video. Il servizio include funzionalità di registrazione, gestione profili, commenti e interazioni sociali.</p>
                              </section>',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_pages');
    }
};
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactFormMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\LegalPage;

class LegalController extends Controller
{
    /**
     * Mostra la pagina contatti
     */
    public function contact()
    {
        $page = LegalPage::getBySlug('contatti');
        return view('legal.contact', compact('page'));
    }

    /**
     * Mostra la privacy policy
     */
    public function privacy()
    {
        $page = LegalPage::getBySlug('privacy');
        return view('legal.privacy', compact('page'));
    }

    /**
     * Mostra i termini di servizio
     */
    public function terms()
    {
        $page = LegalPage::getBySlug('termini');
        return view('legal.terms', compact('page'));
    }

    /**
     * Gestisce l'invio del form di contatto
     */
    public function sendContact(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'oggetto' => 'required|string|max:255',
            'messaggio' => 'required|string',
        ]);

        try {
            $contactData = $request->only(['nome', 'email', 'oggetto', 'messaggio']);
            
            // Invia l'email di notifica all'amministratore
            Mail::to('admin@globio.com')->send(new ContactFormMailable($contactData));
            
            // Log dell'invio per debugging
            Log::info('Email di contatto inviata con successo', [
                'nome' => $contactData['nome'],
                'email' => $contactData['email'],
                'oggetto' => $contactData['oggetto']
            ]);
            
            return back()->with('success', 'Messaggio inviato con successo! Ti risponderemo presto.');
            
        } catch (\Exception $e) {
            // Log dell'errore
            Log::error('Errore nell\'invio dell\'email di contatto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'nome' => $request->input('nome'),
                'email' => $request->input('email')
            ]);
            
            return back()->withInput()->with('error', 'Si è verificato un errore nell\'invio del messaggio. Riprova più tardi.');
        }
    }
}
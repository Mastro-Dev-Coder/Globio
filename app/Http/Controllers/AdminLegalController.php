<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminLegalController extends Controller
{
    /**
     * Display a listing of legal pages
     */
    public function index()
    {
        $legalPages = LegalPage::all();
        return view('admin.legal.index', compact('legalPages'));
    }

    /**
     * Show the form for editing the specified page
     */
    public function edit($slug)
    {
        $legalPage = LegalPage::where('slug', $slug)->firstOrFail();
        
        // Valid slug mapping
        $allowedSlugs = ['contatti', 'privacy', 'termini'];
        if (!in_array($slug, $allowedSlugs)) {
            abort(404);
        }

        return view('admin.legal.edit', compact('legalPage'));
    }

    /**
     * Update the specified page
     */
    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $legalPage = LegalPage::where('slug', $slug)->firstOrFail();
        $legalPage->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.legal.index')
                        ->with('success', 'Pagina legale aggiornata con successo!');
    }

    /**
     * Preview the specified page
     */
    public function preview($slug)
    {
        $legalPage = LegalPage::where('slug', $slug)->firstOrFail();
        
        // Valid slug mapping
        $allowedSlugs = ['contatti', 'privacy', 'termini'];
        if (!in_array($slug, $allowedSlugs)) {
            abort(404);
        }

        return view('admin.legal.preview', compact('legalPage'));
    }

    /**
     * Get all legal pages for API use
     */
    public function apiIndex()
    {
        return response()->json(LegalPage::all());
    }

    /**
     * Get specific legal page by slug
     */
    public function apiShow($slug)
    {
        $legalPage = LegalPage::where('slug', $slug)->first();
        
        if (!$legalPage) {
            return response()->json(['error' => 'Page not found'], 404);
        }

        return response()->json($legalPage);
    }
}
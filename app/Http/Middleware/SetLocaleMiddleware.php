<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['it', 'en', 'es'];
        
        $locale = $request->cookie('locale', session('locale', 'it'));
        
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'it';
        }
        
        App::setLocale($locale);
        
        session(['locale' => $locale]);

        return $next($request);
    }
}

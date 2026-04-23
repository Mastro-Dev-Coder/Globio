<?php

namespace App\Http\Middleware;

use App\Models\ApiAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json([
                'message' => 'Missing API token.',
            ], 401);
        }

        $tokenHash = hash('sha256', $bearerToken);

        $accessToken = ApiAccessToken::with('user')
            ->where('token_hash', $tokenHash)
            ->first();

        if (!$accessToken || $accessToken->isExpired() || !$accessToken->user) {
            return response()->json([
                'message' => 'Invalid or expired API token.',
            ], 401);
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();

        Auth::setUser($accessToken->user);
        $request->attributes->set('api_access_token', $accessToken);

        return $next($request);
    }
}


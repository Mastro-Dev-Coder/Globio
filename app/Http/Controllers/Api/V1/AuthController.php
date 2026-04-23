<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiAccessToken;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => $validated['password'],
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $baseUsername = Str::slug($validated['name'], '-');
        $baseUsername = $baseUsername !== '' ? $baseUsername : 'user';
        $username = $baseUsername;
        $suffix = 1;
        while (UserProfile::where('username', $username)->exists()) {
            $username = $baseUsername . '-' . $suffix;
            $suffix++;
        }

        UserProfile::create([
            'user_id' => $user->id,
            'username' => $username,
            'channel_name' => $validated['name'],
            'channel_description' => '',
            'is_channel_enabled' => true,
        ]);

        $tokenData = $this->issueToken($user, $validated['device_name'] ?? 'flutter-app');

        return response()->json([
            'message' => 'Registration successful.',
            'token' => $tokenData['plain_text_token'],
            'token_type' => 'Bearer',
            'expires_at' => optional($tokenData['model']->expires_at)?->toIso8601String(),
            'user' => $this->serializeUser($user->fresh('userProfile')),
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::with('userProfile')->where('email', strtolower($validated['email']))->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $tokenData = $this->issueToken($user, $validated['device_name'] ?? 'flutter-app');

        return response()->json([
            'message' => 'Login successful.',
            'token' => $tokenData['plain_text_token'],
            'token_type' => 'Bearer',
            'expires_at' => optional($tokenData['model']->expires_at)?->toIso8601String(),
            'user' => $this->serializeUser($user),
        ]);
    }

    public function logout(Request $request)
    {
        /** @var ApiAccessToken|null $accessToken */
        $accessToken = $request->attributes->get('api_access_token');

        if ($accessToken) {
            $accessToken->delete();
        }

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    private function issueToken(User $user, string $deviceName): array
    {
        $plainToken = Str::random(80);
        $expiresAt = now()->addDays((int) env('API_TOKEN_TTL_DAYS', 30));

        $tokenModel = ApiAccessToken::create([
            'user_id' => $user->id,
            'name' => $deviceName,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => $expiresAt,
            'last_used_at' => now(),
        ]);

        return [
            'plain_text_token' => $plainToken,
            'model' => $tokenModel,
        ];
    }

    private function serializeUser(User $user): array
    {
        $profile = $user->userProfile;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'profile' => [
                'username' => $profile?->username,
                'channel_name' => $profile?->channel_name ?? $user->name,
                'channel_description' => $profile?->channel_description,
                'avatar_url' => $this->mediaUrl($profile?->avatar_url),
                'banner_url' => $this->mediaUrl($profile?->banner_url),
                'subscriber_count' => (int) ($profile?->subscriber_count ?? 0),
                'video_count' => (int) ($profile?->video_count ?? 0),
                'total_views' => (int) ($profile?->total_views ?? 0),
            ],
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');

        if (str_starts_with($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }

        return asset('storage/' . $cleanPath);
    }
}


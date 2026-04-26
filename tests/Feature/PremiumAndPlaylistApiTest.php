<?php

namespace Tests\Feature;

use App\Models\ApiAccessToken;
use App\Models\PremiumSubscription;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PremiumAndPlaylistApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_premium_status_for_authenticated_user(): void
    {
        [$user, $token] = $this->makeApiUser();

        PremiumSubscription::create([
            'user_id' => $user->id,
            'provider' => 'stripe',
            'stripe_subscription_id' => 'sub_test_123',
            'plan_code' => 'globio-premium',
            'plan_name' => 'Globio Premium',
            'status' => PremiumSubscription::STATUS_ACTIVE,
            'billing_interval' => 'month',
            'amount' => 1199,
            'currency' => 'eur',
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
            'features' => [
                'ad_free' => true,
                'background_playback' => true,
            ],
        ]);

        $user->forceFill(['premium_access_ends_at' => now()->addMonth()])->save();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/me/premium');

        $response->assertOk()
            ->assertJsonPath('active', true)
            ->assertJsonPath('plan.plan_code', 'globio-premium')
            ->assertJsonPath('features.ad_free', true)
            ->assertJsonPath('badge.short_label', 'Premium');
    }

    public function test_it_creates_playlist_and_adds_video(): void
    {
        [$user, $token] = $this->makeApiUser();
        $video = Video::create([
            'user_id' => $user->id,
            'title' => 'Video Test',
            'video_path' => 'videos/test.mp4',
            'video_url' => 'video-test',
            'status' => 'published',
            'is_public' => true,
            'published_at' => now(),
        ]);

        $createResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/me/playlists', [
                'title' => 'Playlist API',
                'description' => 'Nuova playlist',
                'is_public' => true,
            ]);

        $createResponse->assertCreated()
            ->assertJsonPath('playlist.title', 'Playlist API');

        $playlistId = $createResponse->json('playlist.id');

        $addResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/me/playlists/{$playlistId}/videos", [
                'video_id' => $video->id,
            ]);

        $addResponse->assertCreated();

        $showResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/me/playlists/{$playlistId}");

        $showResponse->assertOk()
            ->assertJsonPath('playlist.video_count', 1)
            ->assertJsonPath('playlist.videos.0.id', $video->id);
    }

    private function makeApiUser(): array
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $plainToken = Str::random(80);

        ApiAccessToken::create([
            'user_id' => $user->id,
            'name' => 'phpunit',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addDay(),
            'last_used_at' => now(),
        ]);

        return [$user, $plainToken];
    }
}

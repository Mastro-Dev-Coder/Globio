<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;

class PlaylistManagementController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => $request->user()->playlists()
                ->withCount('videos')
                ->latest()
                ->get()
                ->map(fn (Playlist $playlist) => $this->serializePlaylist($playlist)),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['nullable', 'boolean'],
            'video_ids' => ['nullable', 'array', 'max:100'],
            'video_ids.*' => ['integer', 'exists:videos,id'],
        ]);

        $playlist = $request->user()->playlists()->create([
            'title' => trim($validated['title']),
            'description' => isset($validated['description']) ? trim((string) $validated['description']) : null,
            'is_public' => (bool) ($validated['is_public'] ?? false),
        ]);

        if (!empty($validated['video_ids'])) {
            $attach = [];
            foreach (array_values($validated['video_ids']) as $index => $videoId) {
                $attach[$videoId] = ['position' => $index + 1];
            }

            $playlist->videos()->syncWithoutDetaching($attach);
            $playlist->refresh();
        }

        $playlist->loadCount('videos');
        $playlist->update(['video_count' => $playlist->videos_count]);

        return response()->json([
            'message' => 'Playlist created.',
            'playlist' => $this->serializePlaylist($playlist->fresh('videos')),
        ], 201);
    }

    public function show(Request $request, Playlist $playlist)
    {
        abort_unless($playlist->user_id === $request->user()->id, 403, 'Unauthorized.');

        $playlist->load(['videos' => fn ($query) => $query->orderBy('playlist_videos.position')])->loadCount('videos');

        return response()->json([
            'playlist' => $this->serializePlaylist($playlist, true),
        ]);
    }

    public function update(Request $request, Playlist $playlist)
    {
        abort_unless($playlist->user_id === $request->user()->id, 403, 'Unauthorized.');

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $playlist->update($validated);
        $playlist->loadCount('videos');

        return response()->json([
            'message' => 'Playlist updated.',
            'playlist' => $this->serializePlaylist($playlist),
        ]);
    }

    public function destroy(Request $request, Playlist $playlist)
    {
        abort_unless($playlist->user_id === $request->user()->id, 403, 'Unauthorized.');

        $playlist->delete();

        return response()->json([
            'message' => 'Playlist deleted.',
        ]);
    }

    public function addVideo(Request $request, Playlist $playlist)
    {
        abort_unless($playlist->user_id === $request->user()->id, 403, 'Unauthorized.');

        $validated = $request->validate([
            'video_id' => ['required', 'integer', 'exists:videos,id'],
        ]);

        $video = Video::findOrFail($validated['video_id']);
        $exists = $playlist->videos()->where('videos.id', $video->id)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Video already exists in this playlist.',
            ], 409);
        }

        $playlist->videos()->attach($video->id, [
            'position' => ((int) $playlist->videos()->count()) + 1,
        ]);

        $this->refreshPlaylistCounts($playlist);

        return response()->json([
            'message' => 'Video added to playlist.',
        ], 201);
    }

    public function removeVideo(Request $request, Playlist $playlist, Video $video)
    {
        abort_unless($playlist->user_id === $request->user()->id, 403, 'Unauthorized.');

        $playlist->videos()->detach($video->id);
        $this->normalizePlaylistPositions($playlist);
        $this->refreshPlaylistCounts($playlist);

        return response()->json([
            'message' => 'Video removed from playlist.',
        ]);
    }

    private function refreshPlaylistCounts(Playlist $playlist): void
    {
        $playlist->update([
            'video_count' => $playlist->videos()->count(),
        ]);
    }

    private function normalizePlaylistPositions(Playlist $playlist): void
    {
        $items = $playlist->videos()
            ->orderBy('playlist_videos.position')
            ->get(['videos.id']);

        foreach ($items->values() as $index => $item) {
            $playlist->videos()->updateExistingPivot($item->id, ['position' => $index + 1]);
        }
    }

    private function serializePlaylist(Playlist $playlist, bool $includeVideos = false): array
    {
        $payload = [
            'id' => $playlist->id,
            'title' => $playlist->title,
            'description' => $playlist->description,
            'thumbnail_url' => $playlist->dynamic_thumbnail_url,
            'is_public' => (bool) $playlist->is_public,
            'video_count' => (int) ($playlist->videos_count ?? $playlist->video_count ?? 0),
            'views_count' => (int) $playlist->views_count,
            'created_at' => optional($playlist->created_at)?->toIso8601String(),
            'updated_at' => optional($playlist->updated_at)?->toIso8601String(),
        ];

        if ($includeVideos) {
            $payload['videos'] = $playlist->videos->map(function (Video $video) {
                return [
                    'id' => $video->id,
                    'slug' => $video->video_url,
                    'title' => $video->title,
                    'thumbnail_url' => $video->thumbnail_url,
                    'duration' => (int) $video->duration,
                    'position' => (int) ($video->pivot?->position ?? 0),
                ];
            })->values();
        }

        return $payload;
    }
}

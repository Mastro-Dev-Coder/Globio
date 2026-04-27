<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\UploadSession;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadWizardController extends Controller
{
    public function __construct(private readonly VideoService $videoService)
    {
    }

    public function createSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file_name' => ['required', 'string', 'max:255'],
            'file_size' => ['required', 'integer', 'min:1'],
            'mime_type' => ['nullable', 'string', 'max:120'],
            'chunk_size' => ['required', 'integer', 'min:262144', 'max:10485760'],
            'total_chunks' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $session = UploadSession::create([
            'user_id' => Auth::id(),
            'token' => (string) Str::uuid(),
            'original_file_name' => $validated['file_name'],
            'mime_type' => $validated['mime_type'] ?? null,
            'total_size' => (int) $validated['file_size'],
            'chunk_size' => (int) $validated['chunk_size'],
            'total_chunks' => (int) $validated['total_chunks'],
            'uploaded_chunks' => [],
            'uploaded_bytes' => 0,
            'status' => 'pending',
            'temp_dir' => 'temp/resumable/' . Str::uuid(),
            'expires_at' => now()->addDay(),
            'last_activity_at' => now(),
        ]);

        Storage::disk('local')->makeDirectory($session->temp_dir . '/chunks');

        return response()->json([
            'session' => $this->serializeSession($session),
        ], 201);
    }

    public function showSession(string $token): JsonResponse
    {
        $session = $this->sessionForUser($token);

        return response()->json([
            'session' => $this->serializeSession($session),
        ]);
    }

    public function uploadChunk(Request $request, string $token): JsonResponse
    {
        $session = $this->sessionForUser($token);

        abort_if(in_array($session->status, ['cancelled', 'completed'], true), 409, 'Upload session is closed.');

        $validated = $request->validate([
            'chunk_index' => ['required', 'integer', 'min:0'],
            'chunk' => ['required', 'file'],
        ]);

        $chunkIndex = (int) $validated['chunk_index'];
        abort_if($chunkIndex >= $session->total_chunks, 422, 'Chunk index out of range.');

        $chunkPath = $session->temp_dir . '/chunks/' . $chunkIndex . '.part';
        $chunkFile = $validated['chunk'];
        $wasUploaded = in_array($chunkIndex, $session->uploaded_chunks ?? [], true);

        Storage::disk('local')->putFileAs(
            dirname($chunkPath),
            $chunkFile,
            basename($chunkPath)
        );

        $uploadedChunks = $session->uploaded_chunks ?? [];
        if (!$wasUploaded) {
            $uploadedChunks[] = $chunkIndex;
            sort($uploadedChunks);
        }

        $session->forceFill([
            'uploaded_chunks' => $uploadedChunks,
            'uploaded_bytes' => $this->calculateUploadedBytes($session),
            'status' => count($uploadedChunks) >= $session->total_chunks ? 'uploaded' : 'uploading',
            'last_activity_at' => now(),
        ])->save();

        return response()->json([
            'session' => $this->serializeSession($session->fresh()),
        ]);
    }

    public function finalize(Request $request, string $token): JsonResponse
    {
        $session = $this->sessionForUser($token);

        abort_if($session->status === 'completed', 409, 'Upload already completed.');
        abort_if(count($session->uploaded_chunks ?? []) < $session->total_chunks, 422, 'Upload is not complete yet.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'tags' => ['nullable', 'string', 'max:500'],
            'language' => ['nullable', 'string', 'size:2'],
            'visibility' => ['required', 'in:public,private,scheduled'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
            'is_reel' => ['nullable', 'boolean'],
            'comments_enabled' => ['nullable', 'boolean'],
            'likes_enabled' => ['nullable', 'boolean'],
            'comments_require_approval' => ['nullable', 'boolean'],
            'playlist_ids' => ['nullable', 'array'],
            'playlist_ids.*' => ['integer', 'exists:playlists,id'],
            'suggested_video_ids' => ['nullable', 'array'],
            'suggested_video_ids.*' => ['integer', 'exists:videos,id'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        if ($validated['visibility'] === 'scheduled' && empty($validated['scheduled_for'])) {
            return response()->json([
                'message' => 'Devi impostare una data di pubblicazione.',
            ], 422);
        }

        $assembledPath = $this->assembleChunks($session);

        $video = $this->videoService->uploadVideoFromResumablePath(
            $assembledPath,
            $session->original_file_name,
            [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'tags' => $validated['tags'] ?? null,
                'language' => $validated['language'] ?? 'it',
                'visibility' => $validated['visibility'],
                'scheduled_for' => $validated['scheduled_for'] ?? null,
                'is_reel' => (bool) ($validated['is_reel'] ?? false),
                'comments_enabled' => (bool) ($validated['comments_enabled'] ?? true),
                'likes_enabled' => (bool) ($validated['likes_enabled'] ?? true),
                'comments_require_approval' => (bool) ($validated['comments_require_approval'] ?? false),
                'suggested_video_ids' => $validated['suggested_video_ids'] ?? [],
            ],
            Auth::id()
        );

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $video->update(['thumbnail_path' => $thumbnailPath]);
        }

        $playlistIds = collect($validated['playlist_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($playlistIds->isNotEmpty()) {
            $playlists = Playlist::where('user_id', Auth::id())
                ->whereIn('id', $playlistIds)
                ->get();

            foreach ($playlists as $playlist) {
                if (!$playlist->videos()->where('videos.id', $video->id)->exists()) {
                    $playlist->videos()->attach($video->id, ['position' => $playlist->videos()->count() + 1]);
                    $playlist->update(['video_count' => $playlist->videos()->count()]);
                }
            }
        }

        $session->forceFill([
            'status' => 'completed',
            'assembled_path' => null,
            'video_id' => $video->id,
            'last_activity_at' => now(),
        ])->save();

        Storage::disk('local')->deleteDirectory($session->temp_dir);

        return response()->json([
            'success' => true,
            'message' => 'Upload completato con successo.',
            'video' => [
                'id' => $video->id,
                'slug' => $video->video_url,
                'status' => $video->status,
                'visibility' => $video->visibility,
                'scheduled_for' => optional($video->scheduled_for)?->toIso8601String(),
                'redirect_url' => route($video->is_reel ? 'reels.show' : 'videos.show', $video),
            ],
        ]);
    }

    public function cancel(string $token): JsonResponse
    {
        $session = $this->sessionForUser($token);

        Storage::disk('local')->deleteDirectory($session->temp_dir);

        $session->forceFill([
            'status' => 'cancelled',
            'uploaded_chunks' => [],
            'uploaded_bytes' => 0,
            'last_activity_at' => now(),
        ])->save();

        $session->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    private function sessionForUser(string $token): UploadSession
    {
        return UploadSession::where('token', $token)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    private function serializeSession(UploadSession $session): array
    {
        $uploadedChunks = $session->uploaded_chunks ?? [];
        $uploadedCount = count($uploadedChunks);

        return [
            'token' => $session->token,
            'file_name' => $session->original_file_name,
            'status' => $session->status,
            'total_size' => (int) $session->total_size,
            'uploaded_bytes' => (int) $session->uploaded_bytes,
            'chunk_size' => (int) $session->chunk_size,
            'total_chunks' => (int) $session->total_chunks,
            'uploaded_chunks' => $uploadedChunks,
            'uploaded_count' => $uploadedCount,
            'progress_percent' => $session->total_chunks > 0
                ? (int) floor(($uploadedCount / $session->total_chunks) * 100)
                : 0,
            'video_id' => $session->video_id,
        ];
    }

    private function calculateUploadedBytes(UploadSession $session): int
    {
        $bytes = 0;

        foreach ($session->uploaded_chunks ?? [] as $chunkIndex) {
            $path = $session->temp_dir . '/chunks/' . $chunkIndex . '.part';
            if (Storage::disk('local')->exists($path)) {
                $bytes += Storage::disk('local')->size($path);
            }
        }

        return $bytes;
    }

    private function assembleChunks(UploadSession $session): string
    {
        $extension = strtolower(pathinfo($session->original_file_name, PATHINFO_EXTENSION) ?: 'mp4');
        $assembledPath = $session->temp_dir . '/assembled.' . $extension;
        $absolutePath = Storage::disk('local')->path($assembledPath);
        $output = fopen($absolutePath, 'wb');

        for ($index = 0; $index < $session->total_chunks; $index++) {
            $chunkPath = $session->temp_dir . '/chunks/' . $index . '.part';
            if (!Storage::disk('local')->exists($chunkPath)) {
                throw new \RuntimeException('Chunk mancante: ' . $index);
            }

            $input = fopen(Storage::disk('local')->path($chunkPath), 'rb');
            stream_copy_to_stream($input, $output);
            fclose($input);
        }

        fclose($output);

        $session->forceFill([
            'assembled_path' => $assembledPath,
            'last_activity_at' => now(),
        ])->save();

        return $absolutePath;
    }
}

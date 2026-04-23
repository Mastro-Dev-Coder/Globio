<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VideoUploadController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function uploadVideo(Request $request)
    {
        $maxVideoUploadMb = (int) \App\Models\Setting::getValue('max_video_upload_mb', 500);
        $maxVideoUploadBytes = $maxVideoUploadMb * 1024 * 1024;
        $maxThumbnailSize = 5 * 1024 * 1024;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'video_file' => ['required', 'mimes:mp4,avi,mov,wmv,flv,webm', 'max:' . $maxVideoUploadBytes],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:' . ($maxThumbnailSize / 1024)],
            'is_public' => ['nullable', 'boolean'],
            'is_reel' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'string', 'max:500'],
            'language' => ['nullable', 'string', 'size:2'],
            'comments_enabled' => ['nullable', 'boolean'],
            'likes_enabled' => ['nullable', 'boolean'],
        ]);

        try {
            $uploadData = $request->except('video_file', 'thumbnail');
            $uploadData['is_reel'] = $request->boolean('is_reel', false);
            $uploadData['is_public'] = $request->boolean('is_public', true);
            $uploadData['comments_enabled'] = $request->boolean('comments_enabled', true);
            $uploadData['likes_enabled'] = $request->boolean('likes_enabled', true);

            $video = $this->videoService->uploadVideo(
                $request->file('video_file'),
                $uploadData,
                Auth::id()
            );

            if ($request->hasFile('thumbnail')) {
                if (!file_exists(public_path('thumbnails'))) {
                    mkdir(public_path('thumbnails'), 0755, true);
                }
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $video->update(['thumbnail_path' => $thumbnailPath]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully! It will be published when processing is complete.',
                'video' => [
                    'id' => $video->id,
                    'slug' => $video->video_url,
                    'title' => $video->title,
                    'status' => $video->status,
                    'is_reel' => (bool) $video->is_reel,
                    'thumbnail_url' => $video->thumbnail_url,
                    'created_at' => optional($video->created_at)?->toIso8601String(),
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Video upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error during upload: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function uploadReel(Request $request)
    {
        return $this->uploadVideo($request);
    }

    public function createPost(Request $request)
    {
        $maxVideoUploadMb = (int) \App\Models\Setting::getValue('max_video_upload_mb', 500);
        $maxVideoUploadBytes = $maxVideoUploadMb * 1024 * 1024;
        $maxThumbnailSize = 5 * 1024 * 1024;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'video_file' => ['required', 'mimes:mp4,avi,mov,wmv,flv,webm', 'max:' . $maxVideoUploadBytes],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:' . ($maxThumbnailSize / 1024)],
            'is_public' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $uploadData = $request->except('video_file', 'thumbnail');
            $uploadData['is_public'] = $request->boolean('is_public', true);
            $uploadData['is_reel'] = true;

            $video = $this->videoService->uploadVideo(
                $request->file('video_file'),
                $uploadData,
                Auth::id()
            );

            if ($request->hasFile('thumbnail')) {
                if (!file_exists(public_path('thumbnails'))) {
                    mkdir(public_path('thumbnails'), 0755, true);
                }
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $video->update(['thumbnail_path' => $thumbnailPath]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reel created successfully!',
                'reel' => [
                    'id' => $video->id,
                    'slug' => $video->video_url,
                    'title' => $video->title,
                    'thumbnail_url' => $video->thumbnail_url,
                    'video_url' => $video->video_file_url,
                    'duration' => $video->duration,
                    'created_at' => optional($video->created_at)?->toIso8601String(),
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Reel upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error during reel upload: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateVideo(Request $request, string $video)
    {
        $video = $this->resolveOwnedVideo($video);
        abort_if(!$video, 404, 'Video not found.');

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_public' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:published,draft,pending_approval'],
            'comments_enabled' => ['nullable', 'boolean'],
            'likes_enabled' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'string', 'max:500'],
        ]);

        $updateData = [];
        foreach (['title', 'description', 'is_public', 'status', 'comments_enabled', 'likes_enabled'] as $field) {
            if (array_key_exists($field, $validated)) {
                if (in_array($field, ['is_public', 'comments_enabled', 'likes_enabled'])) {
                    $updateData[$field] = (bool) $validated[$field];
                } else {
                    $updateData[$field] = $validated[$field];
                }
            }
        }

        if (isset($validated['tags'])) {
            $updateData['tags'] = explode(',', $validated['tags']);
        }

        if (isset($updateData['status']) && $updateData['status'] === 'published' && $video->status !== 'published') {
            $updateData['published_at'] = now();
        }

        $video->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Video updated successfully.',
            'video' => [
                'id' => $video->id,
                'title' => $video->title,
                'description' => $video->description,
                'status' => $video->status,
                'is_public' => (bool) $video->is_public,
            ],
        ]);
    }

    public function deleteVideo(string $video)
    {
        $video = $this->resolveOwnedVideo($video);
        abort_if(!$video, 404, 'Video not found.');

        try {
            $this->videoService->deleteVideo($video);
            return response()->json(['message' => 'Video deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting video: ' . $e->getMessage()], 500);
        }
    }

    public function checkVideoStatus(string $video)
    {
        $video = $this->resolveOwnedVideo($video);
        abort_if(!$video, 404, 'Video not found.');

        return response()->json([
            'status' => $video->status,
            'is_public' => $video->is_public,
            'video_url' => $video->video_url,
            'can_view' => $video->status === 'published' && $video->is_public,
            'thumbnail_url' => $video->thumbnail_url,
            'formatted_duration' => $video->formatted_duration,
            'views_count' => $video->views_count,
            'likes_count' => $video->likes_count,
            'comments_count' => $video->comments_count,
            'updated_at' => $video->updated_at->toISOString(),
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:set_public,set_private,delete'],
            'video_ids' => ['required', 'array'],
            'video_ids.*' => ['exists:videos,id'],
        ]);

        $videoIds = $validated['video_ids'];
        $action = $validated['action'];

        $userVideos = Video::whereIn('id', $videoIds)
            ->where('user_id', Auth::id())
            ->get();

        if ($userVideos->count() !== count($videoIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Some videos were not found or do not belong to you.',
            ], 403);
        }

        try {
            $updatedCount = 0;
            foreach ($userVideos as $video) {
                if ($action === 'set_public') {
                    $video->update(['is_public' => true]);
                    $updatedCount++;
                } elseif ($action === 'set_private') {
                    $video->update(['is_public' => false]);
                    $updatedCount++;
                } elseif ($action === 'delete') {
                    $this->videoService->deleteVideo($video);
                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Operation completed successfully.',
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error during operation: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function resolveOwnedVideo(string $video): ?Video
    {
        return Video::where('user_id', Auth::id())
            ->where(function ($query) use ($video) {
                $query->where('video_url', $video);
                if (is_numeric($video)) {
                    $query->orWhere('id', (int) $video);
                }
            })
            ->first();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PlaylistController extends Controller
{
    /**
     * Crea una nuova playlist
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|min:1',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $playlist = Auth::user()->playlists()->create([
                'title' => trim($request->title),
                'description' => trim($request->description),
                'is_public' => false,
            ]);

            return redirect()->route('playlists')
                ->with('success', 'Playlist creata con successo!');
        } catch (\Exception $e) {
            \Log::error('Errore creazione playlist: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Errore durante la creazione della playlist')
                ->withInput();
        }
    }

    /**
     * Aggiorna una playlist
     */
    public function update(Request $request, $playlistId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|min:1',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $playlist = Auth::user()->playlists()->findOrFail($playlistId);

            $playlist->update([
                'title' => trim($request->title),
                'description' => trim($request->description),
            ]);

            return redirect()->route('playlists')
                ->with('success', 'Playlist aggiornata con successo!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('playlists')
                ->with('error', 'Playlist non trovata');
        } catch (\Exception $e) {
            \Log::error('Errore aggiornamento playlist: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Errore durante l\'aggiornamento della playlist')
                ->withInput();
        }
    }

    /**
     * Elimina una playlist
     */
    public function destroy($playlistId)
    {
        try {
            $playlist = Auth::user()->playlists()->findOrFail($playlistId);
            $playlist->delete();

            return redirect()->route('playlists')
                ->with('success', 'Playlist eliminata con successo!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('playlists')
                ->with('error', 'Playlist non trovata');
        } catch (\Exception $e) {
            \Log::error('Errore eliminazione playlist: ' . $e->getMessage());

            return redirect()->route('playlists')
                ->with('error', 'Errore durante l\'eliminazione della playlist');
        }
    }

    /**
     * Aggiungi un video alla playlist
     */
    public function addVideo(Request $request, $playlistId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:videos,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Video non valido',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $playlist = Auth::user()->playlists()->findOrFail($playlistId);

            // Controlla se il video è già presente nella playlist
            $exists = $playlist->videos()->where('videos.id', $request->video_id)->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Video già presente nella playlist'
                ], 409);
            }

            $playlist->videos()->attach($request->video_id, ['position' => $playlist->videos()->count() + 1]);

            return response()->json([
                'success' => true,
                'message' => 'Video aggiunto alla playlist!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Playlist non trovata'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Errore aggiunta video a playlist: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Errore interno del server'
            ], 500);
        }
    }

    /**
     * Rimuovi un video dalla playlist
     */
    public function removeVideo($playlistId, $videoId): JsonResponse
    {
        try {
            $playlist = Auth::user()->playlists()->findOrFail($playlistId);
            $playlist->videos()->detach($videoId);

            return response()->json([
                'success' => true,
                'message' => 'Video rimosso dalla playlist!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Playlist non trovata'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Errore rimozione video da playlist: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Errore interno del server'
            ], 500);
        }
    }

    /**
     * Mostra una playlist
     */
    public function show(Playlist $playlist)
    {
        try {
            // Verifica se l'utente può visualizzare la playlist
            if (!$playlist->is_public && (!$playlist->user || $playlist->user_id !== Auth::id())) {
                abort(403, 'Non hai accesso a questa playlist');
            }

            // Carica le relazioni
            $playlist->load(['user', 'user.userProfile', 'videos' => function ($query) {
                $query->published()
                    ->orderBy('playlist_videos.position', 'asc');
            }]);

            $playlist->incrementViews();

            $relatedPlaylists = $this->getRelatedPlaylists($playlist, 4);

            // Get video from URL parameter or use first video
            $playlistVideos = $playlist->videos;
            $currentVideo = null;
            $currentIndex = 0;

            // Check if there's a video parameter in the URL
            $videoId = request()->get('video');
            if ($videoId) {
                foreach ($playlistVideos as $index => $video) {
                    if ($video->id == $videoId) {
                        $currentVideo = $video;
                        $currentIndex = $index;
                        break;
                    }
                }
            }

            // If no video found, use the first video
            if (!$currentVideo && $playlistVideos->isNotEmpty()) {
                $currentVideo = $playlistVideos[0];
                $currentIndex = 0;
            }

            // Get next video in playlist
            $nextPlaylistVideo = null;
            if ($currentIndex < $playlistVideos->count() - 1) {
                $nextPlaylistVideo = $playlistVideos[$currentIndex + 1];
            } else {
                // Try to get next video from database using position
                $currentPlaylistVideo = \App\Models\PlaylistVideo::where('playlist_id', $playlist->id)
                    ->where('video_id', $currentVideo->id)
                    ->first();
                if ($currentPlaylistVideo) {
                    $nextPlaylistVideoRecord = \App\Models\PlaylistVideo::where('playlist_id', $playlist->id)
                        ->where('position', '>', $currentPlaylistVideo->position)
                        ->orderBy('position', 'asc')
                        ->first();
                    if ($nextPlaylistVideoRecord) {
                        $nextPlaylistVideo = \App\Models\Video::published()->find($nextPlaylistVideoRecord->video_id);
                    }
                }
            }

            return view('playlists.show', compact(
                'playlist',
                'relatedPlaylists',
                'playlistVideos',
                'currentVideo',
                'nextPlaylistVideo',
                'currentIndex'
            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Playlist non trovata');
        } catch (\Exception $e) {
            Log::error('Errore caricamento playlist: ' . $e->getMessage());
            abort(500, 'Errore interno del server');
        }
    }

    /**
     * Ottieni i dettagli di una playlist (API)
     */
    public function getPlaylist($playlistId): JsonResponse
    {
        try {
            $playlist = Auth::user()->playlists()
                ->with(['videos' => function ($query) {
                    $query->orderBy('playlist_videos.position', 'asc');
                }])
                ->findOrFail($playlistId);

            return response()->json([
                'success' => true,
                'playlist' => $playlist
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Playlist non trovata'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Errore caricamento playlist: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Errore interno del server'
            ], 500);
        }
    }

    /**
     * Ottieni playlist simili
     */
    private function getRelatedPlaylists(Playlist $playlist, int $limit): Collection
    {
        // Estrai tag dalla playlist
        $playlistTags = $this->getPlaylistTags($playlist);

        // Recupera playlist simili
        $relatedPlaylists = Playlist::public()
            ->with(['user', 'user.userProfile', 'videos' => function ($query) {
                $query->published();
            }])
            ->where('id', '!=', $playlist->id)
            ->whereHas('videos', function ($query) {
                $query->published();
            })
            ->get()
            ->filter(function ($related) use ($playlistTags) {
                $relatedTags = $this->getPlaylistTags($related);
                return count(array_intersect($playlistTags, $relatedTags)) > 0;
            })
            ->sortByDesc(function ($related) use ($playlistTags) {
                $relatedTags = $this->getPlaylistTags($related);
                return count(array_intersect($playlistTags, $relatedTags));
            })
            ->take($limit);

        // Se non ci sono playlist simili, restituisci playlist popolari
        if ($relatedPlaylists->isEmpty()) {
            return Playlist::public()
                ->with(['user', 'user.userProfile', 'videos'])
                ->where('id', '!=', $playlist->id)
                ->whereHas('videos', function ($query) {
                    $query->published();
                })
                ->orderBy('views_count', 'desc')
                ->limit($limit)
                ->get();
        }

        return $relatedPlaylists;
    }

    /**
     * Estrae i tag da una playlist
     */
    private function getPlaylistTags(Playlist $playlist): array
    {
        $tags = [];

        foreach ($playlist->videos as $video) {
            $videoTags = $video->tags ?? [];
            $tags = array_merge($tags, $videoTags);
        }

        return array_unique($tags);
    }

    /**
     * Ottieni tutte le playlist dell'utente
     */
    public function userPlaylists(Request $request): JsonResponse
    {
        try {
            $query = Auth::user()->playlists()
                ->with(['videos' => function ($query) {
                    $query->orderBy('playlist_videos.position', 'asc');
                }])
                ->withCount('videos')
                ->orderBy('created_at', 'desc');

            // Se viene richiesto un ID specifico, restituisci solo quella playlist
            if ($request->has('id')) {
                $playlist = $query->where('id', $request->id)->get();
                return response()->json([
                    'success' => true,
                    'playlists' => $playlist
                ]);
            }

            $playlists = $query->get();

            return response()->json([
                'success' => true,
                'playlists' => $playlists
            ]);
        } catch (\Exception $e) {
            \Log::error('Errore caricamento playlist utente: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Errore interno del server',
                'playlists' => []
            ], 500);
        }
    }
}

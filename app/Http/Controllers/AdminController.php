<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Comment;
use App\Models\Setting;

use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_videos' => Video::count(),
            'published_videos' => Video::published()->count(),
            'pending_videos' => Video::where('status', 'processing')->count(),
            'total_views' => Video::published()->sum('views_count'),
            'total_comments' => Comment::count(),
            'total_notifications' => DB::table('notifications')->count(),
        ];

        $recentVideos = Video::with(['user.userProfile'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activeUsers = User::with('userProfile')
            ->withCount(['videos', 'subscribers'])
            ->orderBy('videos', 'desc')
            ->limit(10)
            ->get();

        $trendingVideos = Video::published()
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentVideos', 'activeUsers', 'trendingVideos'));
    }

    public function users(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $sortBy = $request->get('sort', 'newest');

        $query = User::with('userProfile');

        // Ricerca
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Ordinamento
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('name');
                break;
            case 'videos':
                $query->withCount('videos')->orderBy('videos_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $users = $query->paginate(20);

        return view('admin.users', compact('users', 'search', 'role', 'sortBy'));
    }

    public function showUser(User $user)
    {
        $user->load(['userProfile', 'videos', 'comments']);

        $stats = [
            'videos_count' => $user->videos()->count(),
            'published_videos' => $user->videos()->published()->count(),
            'subscribers_count' => $user->subscribers()->count(),
            'total_views' => $user->videos()->published()->sum('views_count'),
            'comments_count' => $user->comments()->count(),
        ];

        return view('admin.user-details', compact('user', 'stats'));
    }

    public function editUser(User $user)
    {
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'nullable|in:user,admin,moderator',
            'is_verified' => 'boolean',
            'channel_name' => 'nullable|string|max:255',
            'channel_description' => 'nullable|string|max:1000',
        ]);

        try {
            // Aggiorna utente
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'is_verified' => $request->boolean('is_verified'),
            ]);

            // Aggiorna ruolo se specificato
            if ($request->role) {
                // Implementa logica per ruoli
            }

            // Aggiorna profilo se esiste
            if ($user->userProfile) {
                $user->userProfile->update([
                    'channel_name' => $request->channel_name,
                    'channel_description' => $request->channel_description,
                    'is_verified' => $request->boolean('is_verified'),
                ]);
            }

            return redirect()->route('admin.users')
                ->with('success', 'Utente aggiornato con successo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()]);
        }
    }

    public function deleteUser(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('admin.users')
                ->with('success', 'Utente eliminato con successo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante l\'eliminazione: ' . $e->getMessage()]);
        }
    }

    public function videos(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');
        $sortBy = $request->get('sort', 'newest');

        $query = Video::with(['user.userProfile']);

        // Filtro per status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Ricerca
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Ordinamento
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'views':
                $query->orderBy('views_count', 'desc');
                break;
            case 'duration':
                $query->orderBy('duration', 'desc');
                break;
            default:
                $query->latest();
        }

        $videos = $query->paginate(20);

        return view('admin.videos', compact('videos', 'status', 'search', 'sortBy'));
    }

    public function moderateVideo(Request $request, Video $video)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            if ($request->action === 'approve') {
                $video->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'moderation_reason' => null,
                ]);
                $message = 'Video approvato con successo!';
            } else {
                $video->update([
                    'status' => 'rejected',
                    'moderation_reason' => $request->reason ?: 'Video respinto dall\'amministratore',
                ]);
                $message = 'Video respinto con successo!';
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante la moderazione: ' . $e->getMessage()]);
        }
    }

    public function deleteVideo(Video $video)
    {
        try {
            $videoService = new \App\Services\VideoService();
            $videoService->deleteVideo($video);

            return redirect()->route('admin.videos')
                ->with('success', 'Video eliminato con successo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante l\'eliminazione: ' . $e->getMessage()]);
        }
    }

    /**
     * Approva più video in gruppo
     */
    public function bulkApproveVideos(Request $request)
    {
        $request->validate([
            'video_ids' => 'required|array|min:1',
            'video_ids.*' => 'exists:videos,id'
        ]);

        try {
            $videoIds = $request->video_ids;
            $videos = Video::whereIn('id', $videoIds)->where('status', 'processing')->get();

            if ($videos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nessun video trovato da approvare.'
                ], 404);
            }

            $updatedCount = 0;
            foreach ($videos as $video) {
                $video->update([
                    'status' => 'published',
                    'published_at' => now(),
                    'moderation_reason' => null,
                ]);
                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} video approvati con successo!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'approvazione: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rifiuta più video in gruppo
     */
    public function bulkRejectVideos(Request $request)
    {
        $request->validate([
            'video_ids' => 'required|array|min:1',
            'video_ids.*' => 'exists:videos,id',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $videoIds = $request->video_ids;
            $videos = Video::whereIn('id', $videoIds)->where('status', 'processing')->get();

            if ($videos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nessun video trovato da rifiutare.'
                ], 404);
            }

            $updatedCount = 0;
            foreach ($videos as $video) {
                $video->update([
                    'status' => 'rejected',
                    'moderation_reason' => $request->reason ?: 'Video respinti in gruppo dall\'amministratore'
                ]);
                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} video respinti con successo!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il rifiuto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function comments(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $query = Comment::with(['user.userProfile', 'video']);

        // Ricerca
        if ($search) {
            $query->where('content', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%');
                });
        }

        $comments = $query->latest()->paginate(30);

        return view('admin.comments', compact('comments', 'search', 'status'));
    }

    public function deleteComment(Comment $comment)
    {
        try {
            $comment->delete();
            return back()->with('success', 'Commento eliminato con successo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante l\'eliminazione: ' . $e->getMessage()]);
        }
    }

    public function statistics()
    {
        $period = request()->get('period', 'month');

        switch ($period) {
            case 'week':
                $startDate = now()->subWeek();
                break;
            case 'month':
                $startDate = now()->subMonth();
                break;
            case 'year':
                $startDate = now()->subYear();
                break;
            default:
                $startDate = now()->subMonth();
        }

        $stats = [
            'users' => [
                'total' => User::count(),
                'new' => User::where('created_at', '>=', $startDate)->count(),
            ],
            'videos' => [
                'total' => Video::count(),
                'published' => Video::published()->count(),
                'new' => Video::where('created_at', '>=', $startDate)->count(),
                'pending' => Video::where('status', 'processing')->count(),
            ],
            'views' => [
                'total' => Video::published()->sum('views_count'),
                'new' => Video::where('published_at', '>=', $startDate)->sum('views_count'),
            ],
            'comments' => [
                'total' => Comment::count(),
                'new' => Comment::where('created_at', '>=', $startDate)->count(),
            ],
        ];

        return view('admin.statistics', compact('stats', 'period'));
    }

    public function settings()
    {
        $settings = [
            'site_name' => config('app.name'),
            'max_upload_size' => Setting::getValue('max_video_upload_mb', 500),
            'allowed_video_formats' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'],
            'require_approval' => Setting::getValue('require_approval', false),
            'enable_comments' => Setting::getValue('enable_comments', true),
            'enable_likes' => Setting::getValue('enable_likes', true),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'max_upload_size' => 'required|integer|min:50|max:2000',
            'require_approval' => 'boolean',
            'enable_comments' => 'boolean',
            'enable_likes' => 'boolean',
        ]);

        try {
            config(['app.name' => $request->site_name]);
            config(['video.max_upload_size' => $request->max_upload_size]);
            config(['video.require_approval' => $request->boolean('require_approval')]);

            return back()->with('success', 'Configurazioni aggiornate con successo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()]);
        }
    }

    /**
     * Gestione pubblicità
     */
    public function advertisements(Request $request)
    {
        $type = $request->get('type');
        $position = $request->get('position');
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $query = Advertisement::query();

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($position && $position !== 'all') {
            $query->where('position', $position);
        }

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($status !== 'all') {
            if ($status === 'active') {
                $query->currentlyActive();
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $advertisements = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Advertisement::count(),
            'active' => Advertisement::currentlyActive()->count(),
            'banner' => Advertisement::byType('banner')->count(),
            'adsense' => Advertisement::byType('adsense')->count(),
            'video' => Advertisement::byType('video')->count(),
        ];

        return view('admin.advertisements.advertisements', compact('advertisements', 'stats', 'type', 'position', 'search', 'status'));
    }

    public function createAdvertisement()
    {
        return view('admin.advertisements.advertisement-create');
    }

    public function storeAdvertisement(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:banner,adsense,video',
            'position' => 'required|in:header,sidebar,footer,between_videos,video_overlay',
            'content' => 'nullable|string',
            'code' => 'nullable|string',
            'link_url' => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'priority' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            $data = $request->all();
            $data['is_active'] = $request->boolean('is_active', true);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/advertisements', $imageName);
                $data['image_url'] = 'advertisements/' . $imageName;
            }

            Advertisement::create($data);

            return redirect()->route('admin.advertisements.advertisements')
                ->with('success', 'Pubblicità creata con successo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante la creazione: ' . $e->getMessage()]);
        }
    }

    public function editAdvertisement(Advertisement $advertisement)
    {
        return view('admin.advertisements.advertisement-edit', compact('advertisement'));
    }

    public function updateAdvertisement(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:banner,adsense,video',
            'position' => 'required|in:header,sidebar,footer,between_videos,video_overlay',
            'content' => 'nullable|string',
            'code' => 'nullable|string',
            'link_url' => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'priority' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            $data = $request->all();
            $data['is_active'] = $request->boolean('is_active', true);

            if ($request->hasFile('image')) {
                if ($advertisement->image_url) {
                    Storage::disk('public')->delete($advertisement->image_url);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/advertisements', $imageName);
                $data['image_url'] = 'advertisements/' . $imageName;
            }

            $advertisement->update($data);

            return redirect()->route('admin.advertisements.advertisements')
                ->with('success', 'Pubblicità aggiornata con successo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()]);
        }
    }

    public function deleteAdvertisement(Advertisement $advertisement)
    {
        try {
            // Elimina immagine se esiste
            if ($advertisement->image_url) {
                Storage::disk('public')->delete($advertisement->image_url);
            }

            $advertisement->delete();

            return redirect()->route('admin.advertisements.advertisements')
                ->with('success', 'Pubblicità eliminata con successo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante l\'eliminazione: ' . $e->getMessage()]);
        }
    }

    public function toggleAdvertisementStatus(Advertisement $advertisement)
    {
        try {
            $advertisement->update(['is_active' => !$advertisement->is_active]);

            $status = $advertisement->is_active ? 'attivata' : 'disattivata';
            return back()->with('success', "Pubblicità {$status} con successo!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante il cambio di stato: ' . $e->getMessage()]);
        }
    }

    public function advertisementStats(Advertisement $advertisement)
    {
        return view('admin.advertisements.advertisement-stats', compact('advertisement'));
    }

    /**
     * Traccia i click sulle pubblicità (API)
     */
    public function trackAdvertisementClick(Advertisement $advertisement)
    {
        try {
            $advertisement->incrementClicks();

            return response()->json([
                'success' => true,
                'message' => 'Click tracciato con successo'
            ]);
        } catch (\Exception $e) {
            Log::error('Errore nel tracciamento click pubblicità: ' . $e->getMessage(), [
                'advertisement_id' => $advertisement->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore nel tracciamento'
            ], 500);
        }
    }

    /**
     * Mostra le impostazioni delle pubblicità video (esteso)
     */
    public function advertisementSettings()
    {
        $settings = [
            'ads_pre_roll_enabled' => Setting::getBooleanValue('ads_pre_roll_enabled', true),
            'ads_mid_roll_enabled' => Setting::getBooleanValue('ads_mid_roll_enabled', true),
            'ads_post_roll_enabled' => Setting::getBooleanValue('ads_post_roll_enabled', true),
            'ads_skip_delay' => (int) Setting::getValue('ads_skip_delay', 5),
            'ads_mid_roll_positions' => Setting::getValue('ads_mid_roll_positions', '25,50,75'),
            'ads_max_per_video' => (int) Setting::getValue('ads_max_per_video', 3),
            'ads_frequency_cap' => (int) Setting::getValue('ads_frequency_cap', 1),

            'ads_vast_enabled' => Setting::getBooleanValue('ads_vast_enabled', false),
            'ads_vast_source_type' => Setting::getValue('ads_vast_source_type', 'url'),
            'ads_vast_urls' => Setting::getValue('ads_vast_urls', ''),
            'ads_vast_xml' => Setting::getValue('ads_vast_xml', ''),
            'ads_vast_clickthrough_url' => Setting::getValue('ads_vast_clickthrough_url', ''),
            'ads_vast_custom_text' => Setting::getValue('ads_vast_custom_text', ''),
            
            'ads_vmap_enabled' => Setting::getBooleanValue('ads_vmap_enabled', false),
            'ads_vmap_source_type' => Setting::getValue('ads_vmap_source_type', 'url'),
            'ads_vmap_urls' => Setting::getValue('ads_vmap_urls', ''),
            'ads_vmap_xml' => Setting::getValue('ads_vmap_xml', ''),
            
            'ads_google_adsense_enabled' => Setting::getBooleanValue('ads_google_adsense_enabled', false),
            'ads_google_ad_client' => Setting::getValue('ads_google_ad_client', ''),
            'ads_google_ad_slot' => Setting::getValue('ads_google_ad_slot', ''),
            'ads_google_ad_format' => Setting::getValue('ads_google_ad_format', 'video'),
            'ads_google_ad_theme' => Setting::getValue('ads_google_ad_theme', 'dark'),
            'ads_google_ad_language' => Setting::getValue('ads_google_ad_language', 'it'),
        ];

        // Log per debug - verifica che il valore venga letto correttamente
        Log::info('Impostazioni caricate per la vista', [
            'ads_vast_source_type' => $settings['ads_vast_source_type'],
            'ads_vmap_source_type' => $settings['ads_vmap_source_type'],
        ]);

        return view('admin.advertisements.advertisement-settings', compact('settings'));
    }

    /**
     * Aggiorna le impostazioni delle pubblicità video (esteso)
     */
    public function updateAdvertisementSettings(Request $request)
    {
        Log::info('updateAdvertisementSettings chiamato', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
        ]);
        
        try {
            // Log per debug - tutti i dati ricevuti dalla richiesta
            Log::info('Dati ricevuti dalla richiesta', [
                'all_request_data' => $request->all(),
                'ads_vast_source_type' => $request->input('ads_vast_source_type'),
                'ads_vmap_source_type' => $request->input('ads_vmap_source_type'),
            ]);

            $vastSourceType = $request->input('ads_vast_source_type', 'url');
            $vmapSourceType = $request->input('ads_vmap_source_type', 'url');
            
            // Log per debug
            Log::info('Aggiornamento impostazioni VAST', [
                'vast_source_type_from_request' => $request->input('ads_vast_source_type'),
                'vast_source_type_variable' => $vastSourceType,
                'vast_urls_from_request' => $request->input('ads_vast_urls'),
                'vast_xml_from_request' => $request->input('ads_vast_xml'),
            ]);
            
            $settings = [
                'ads_pre_roll_enabled' => $request->has('ads_pre_roll_enabled'),
                'ads_mid_roll_enabled' => $request->has('ads_mid_roll_enabled'),
                'ads_post_roll_enabled' => $request->has('ads_post_roll_enabled'),
                'ads_skip_delay' => $request->input('ads_skip_delay', 5),
                'ads_mid_roll_positions' => $request->input('ads_mid_roll_positions', '25,50,75'),
                'ads_max_per_video' => $request->input('ads_max_per_video', 3),
                'ads_frequency_cap' => $request->input('ads_frequency_cap', 1),

                'ads_vast_enabled' => $request->has('ads_vast_enabled'),
                'ads_vast_source_type' => $vastSourceType,
                'ads_vast_urls' => $vastSourceType === 'url' ? $request->input('ads_vast_urls', '') : '',
                'ads_vast_xml' => $vastSourceType === 'xml' ? $request->input('ads_vast_xml', '') : '',
                'ads_vast_clickthrough_url' => $request->input('ads_vast_clickthrough_url', ''),
                'ads_vast_custom_text' => $request->input('ads_vast_custom_text', ''),
                
                'ads_vmap_enabled' => $request->has('ads_vmap_enabled'),
                'ads_vmap_source_type' => $vmapSourceType,
                'ads_vmap_urls' => $vmapSourceType === 'url' ? $request->input('ads_vmap_urls', '') : '',
                'ads_vmap_xml' => $vmapSourceType === 'xml' ? $request->input('ads_vmap_xml', '') : '',
                
                'ads_google_adsense_enabled' => $request->has('ads_google_adsense_enabled'),
                'ads_google_ad_client' => $request->input('ads_google_ad_client', ''),
                'ads_google_ad_slot' => $request->input('ads_google_ad_slot', ''),
                'ads_google_ad_format' => $request->input('ads_google_ad_format', 'video'),
                'ads_google_ad_theme' => $request->input('ads_google_ad_theme', 'dark'),
                'ads_google_ad_language' => $request->input('ads_google_ad_language', 'it'),
            ];

            if ($settings['ads_skip_delay'] < 0 || $settings['ads_skip_delay'] > 60) {
                return back()->withErrors(['ads_skip_delay' => 'Il ritardo skip deve essere tra 0 e 60 secondi']);
            }

            if ($settings['ads_max_per_video'] < 1 || $settings['ads_max_per_video'] > 10) {
                return back()->withErrors(['ads_max_per_video' => 'Il massimo ads per video deve essere tra 1 e 10']);
            }

            if ($settings['ads_frequency_cap'] < 1 || $settings['ads_frequency_cap'] > 5) {
                return back()->withErrors(['ads_frequency_cap' => 'La frequenza massima deve essere tra 1 e 5']);
            }

            if ($settings['ads_google_adsense_enabled']) {
                if (empty($settings['ads_google_ad_client']) || empty($settings['ads_google_ad_slot'])) {
                    return back()->withErrors([
                        'google_adsense' => 'Per Google AdSense sono richiesti sia Ad Client ID che Ad Slot ID'
                    ]);
                }
            }

            // Validazione VAST: se abilitato, richiede il campo appropriato in base al tipo
            // NOTA: Permettiamo il salvataggio del tipo di sorgente anche se il contenuto è vuoto
            // per permettere all'utente di cambiare il tipo senza dover fornire immediatamente il contenuto
            if ($settings['ads_vast_enabled']) {
                if ($settings['ads_vast_source_type'] === 'url') {
                    if (empty($settings['ads_vast_urls'])) {
                        // Non blocchiamo il salvataggio, ma salviamo il tipo di sorgente
                        // L'utente dovrà fornire gli URL prima di usare VAST
                    }
                } elseif ($settings['ads_vast_source_type'] === 'xml') {
                    if (empty($settings['ads_vast_xml'])) {
                        // Non blocchiamo il salvataggio, ma salviamo il tipo di sorgente
                        // L'utente dovrà fornire l'XML prima di usare VAST
                    } else {
                        // Validiamo l'XML solo se è stato fornito
                        if (!str_contains($settings['ads_vast_xml'], '<VAST')) {
                            return back()->withErrors(['vast_xml' => 'Il XML VAST deve contenere un elemento <VAST> valido']);
                        }
                    }
                }
            }

            // Validazione VMAP: se abilitato, richiede il campo appropriato in base al tipo
            // NOTA: Permettiamo il salvataggio del tipo di sorgente anche se il contenuto è vuoto
            if ($settings['ads_vmap_enabled']) {
                if ($settings['ads_vmap_source_type'] === 'url') {
                    if (empty($settings['ads_vmap_urls'])) {
                        // Non blocchiamo il salvataggio, ma salviamo il tipo di sorgente
                        // L'utente dovrà fornire gli URL prima di usare VMAP
                    }
                } elseif ($settings['ads_vmap_source_type'] === 'xml') {
                    if (empty($settings['ads_vmap_xml'])) {
                        // Non blocchiamo il salvataggio, ma salviamo il tipo di sorgente
                        // L'utente dovrà fornire l'XML prima di usare VMAP
                    } else {
                        // Validiamo l'XML solo se è stato fornito
                        if (!str_contains($settings['ads_vmap_xml'], '<vmap:VMAP') && !str_contains($settings['ads_vmap_xml'], '<VMAP')) {
                            return back()->withErrors(['vmap_xml' => 'Il XML VMAP deve contenere un elemento <vmap:VMAP> o <VMAP> valido']);
                        }
                    }
                }
            }

            foreach ($settings as $key => $value) {
                // Log per debug - prima del salvataggio
                Log::info('Salvataggio impostazione', [
                    'key' => $key,
                    'value' => $value,
                    'value_type' => gettype($value),
                ]);
                
                Setting::setValue($key, $value);
                
                // Log per debug - dopo il salvataggio
                $savedValue = Setting::getValue($key, 'default');
                Log::info('Impostazione salvata', [
                    'key' => $key,
                    'saved_value' => $savedValue,
                    'saved_value_type' => gettype($savedValue),
                ]);
            }

            // Log per debug - verifica salvataggio finale
            Log::info('Impostazioni salvate con successo', [
                'ads_vast_source_type_saved' => Setting::getValue('ads_vast_source_type', 'default'),
                'ads_vmap_source_type_saved' => Setting::getValue('ads_vmap_source_type', 'default'),
            ]);

            return redirect()->route('admin.advertisements.advertisements.settings')
                ->with('success', 'Impostazioni delle pubblicità aggiornate con successo!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()]);
        }
    }
}

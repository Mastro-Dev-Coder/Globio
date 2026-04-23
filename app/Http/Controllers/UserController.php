<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChannelAnalytics;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Report;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserProfile;
use App\Models\Video;
use App\Models\WatchHistory;
use App\Models\WatchLater;
use App\Models\CreatorFeedback;
use App\Notifications\NewSubscriberNotification;
use App\Services\VideoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Mostra il profilo dell'utente
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load('userProfile');

        $userId = $user->id;

        $preferences = [
            'notifications' => UserPreference::getCategoryPreferences($userId, 'notifications'),
            'app_preferences' => UserPreference::getCategoryPreferences($userId, 'app_preferences'),
            'privacy' => UserPreference::getCategoryPreferences($userId, 'privacy'),
        ];

        return view('users.profile', compact('user', 'preferences'));
    }

    /**
     * Mostra le impostazioni utente
     */
    public function userSettings()
    {
        $user = Auth::user();
        $user->load('userProfile');

        $userId = $user->id;

        $preferences = [
            'notifications' => UserPreference::getCategoryPreferences($userId, 'notifications'),
            'app_preferences' => UserPreference::getCategoryPreferences($userId, 'app_preferences'),
            'privacy' => UserPreference::getCategoryPreferences($userId, 'privacy'),
        ];

        return view('users.user-settings', compact('user', 'preferences'));
    }

    /**
     * Mostra il canale di un utente
     */
    public function channel($channel_name)
    {
        $UserProfile = UserProfile::where('channel_name', $channel_name)->first();

        if (!$UserProfile) {
            abort(404, 'Canale non trovato');
        }

        $UserProfile->load('user');

        if (is_null($UserProfile->is_channel_enabled)) {
            $UserProfile->is_channel_enabled = true;
            $UserProfile->save();
        }

        if ($UserProfile->is_channel_enabled === false) {
            abort(404, 'Canale disattivato');
        }

        $isSubscribed = false;
        if (Auth::check()) {
            $isSubscribed = Subscription::where('subscriber_id', Auth::id())
                ->where('channel_id', $UserProfile->user_id)
                ->exists();
        }

        $videos = $UserProfile->videos()
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $playlists = $UserProfile->playlists()
            ->with(['videos' => function ($query) {
                $query->limit(4)->orderBy('playlist_videos.position', 'asc');
            }])
            ->withCount('videos')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'videos_count' => $UserProfile->videos()->published()->count(),
            'subscribers_count' => $UserProfile->subscribers()->count(),
            'total_views' => $UserProfile->videos()->published()->sum('views_count'),
        ];

        return view('users.channel', compact('UserProfile', 'videos', 'playlists', 'stats', 'isSubscribed'));
    }

    /**
     * Modifica il canale dell'utente (stile YouTube)
     */
    public function channelEdit($channel_name = null)
    {
        $user = Auth::user();

        if ($channel_name) {
            $userProfile = UserProfile::where('channel_name', $channel_name)
                ->where('user_id', $user->id)
                ->first();

            if (!$userProfile) {
                abort(404, 'Canale non trovato o non autorizzato');
            }
        } else {
            $userProfile = $user->userProfile;
        }

        $stats = [
            'videos_count' => $user->videos()->published()->count(),
            'subscribers_count' => $user->subscribers()->count(),
            'total_views' => $user->videos()->published()->sum('views_count'),
            'total_likes' => $user->videos()->published()->sum('likes_count'),
        ];

        $recentVideos = $user->videos()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $latestVideo = $recentVideos->first();

        $period = request('period', '30');
        $startDate = now()->subDays($period)->toDateString();
        $endDate = now()->toDateString();

        $channelStats = ChannelAnalytics::getChannelStats($user->id, $startDate, $endDate);

        $dailyStats = $this->getDailyStatsForChart($user->id, $startDate, $endDate);

        $topVideos = ChannelAnalytics::getTopVideos($user->id, 5, $startDate, $endDate);

        $trafficSources = ChannelAnalytics::getTrafficSources($user->id, $startDate, $endDate);

        $demographics = ChannelAnalytics::getDemographics($user->id, $startDate, $endDate);

        $topVideosFallback = $user->videos()->published()->orderBy('views_count', 'desc')->limit(5)->get();
        $recentSubscribers = $this->getRecentSubscribers($user->id);
        $recentComments = $this->getRecentComments($user->id);
        $communityStats = $this->getCommunityStats($user->id);

        $creatorReports = Report::where('reported_user_id', Auth::id())
            ->orWhere('channel_id', Auth::id())
            ->with(['reporter', 'video', 'comment'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $creator = CreatorFeedback::where('creator_id', Auth::id())
            ->with(['admin', 'report'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $totalReports = Report::where('reported_user_id', Auth::id())
            ->orWhere('channel_id', Auth::id())
            ->count();
        $pendingReports = Report::where('reported_user_id', Auth::id())
            ->orWhere('channel_id', Auth::id())
            ->where('status', 'pending')
            ->count();
        $unread = CreatorFeedback::where('creator_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('users.channel-edit', compact(
            'userProfile',
            'user',
            'stats',
            'recentVideos',
            'latestVideo',
            'channelStats',
            'dailyStats',
            'topVideos',
            'trafficSources',
            'demographics',
            'period',
            'topVideosFallback',
            'recentSubscribers',
            'recentComments',
            'communityStats',
            'creatorReports',
            'creator',
            'totalReports',
            'pendingReports',
            'unread'
        ));
    }

    /**
     * Aggiorna il profilo dell'utente
     */
    public function updateChannel(Request $request)
    {
        $user = Auth::user();
        $userProfile = $user->userProfile;

        $request->validate([
            'channel_name' => [
                'nullable',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-_àèéìíîòóùúÀÈÉÌÍÎÒÓÙÚ]+$/',
                'unique:user_profiles,channel_name,' . ($userProfile->id ?? 'NULL')
            ],
            'username' => [
                'nullable',
                'string',
                'max:255',
                'unique:user_profiles,username,' . ($userProfile->id ?? 'NULL')
            ],
            'channel_description' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);


        $updateData = [];

        $allowedFields = [
            'username',
            'channel_name',
            'channel_description',
            'social_links',
            'country'
        ];

        foreach ($allowedFields as $field) {
            $hasField = $request->has($field);
            $inputValue = $request->input($field);

            Log::info("Debug campo {$field}: has={$hasField}, value=" . ($inputValue !== null ? "'{$inputValue}'" : 'null') . ", empty=" . ($inputValue === '' ? 'true' : 'false'));

            if ($hasField) {
                $value = $inputValue === '' ? null : $inputValue;
                $updateData[$field] = $value;
                Log::info("Campo {$field} aggiunto a updateData: " . ($value ?: 'null'));
            } else {
                Log::info("Campo {$field} non presente nella request");
            }
        }

        if ($request->has('is_channel_enabled')) {
            $updateData['is_channel_enabled'] = $request->boolean('is_channel_enabled');
            Log::info('is_channel_enabled impostato a: ' . $updateData['is_channel_enabled']);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar_url'] = $path;
            Log::info('Avatar caricato: ' . $path);
        }

        if ($request->has('remove_avatar') && $request->remove_avatar == '1') {
            if ($userProfile && $userProfile->avatar_url) {
                Storage::delete($userProfile->avatar_url);
                $updateData['avatar_url'] = null;
                Log::info('Avatar rimosso dal storage e aggiornato nel database');
            }
        }

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banners', 'public');
            $updateData['banner_url'] = $path;
            Log::info('Banner caricato: ' . $path);
        }

        // Rimozione banner
        if ($request->has('remove_banner') && $request->remove_banner == '1') {
            if ($userProfile && $userProfile->banner_url) {
                Storage::delete($userProfile->banner_url);
                $updateData['banner_url'] = null;
                Log::info('Banner rimosso dal storage e aggiornato nel database');
            }
        }

        Log::info('Dati finali da aggiornare: ', $updateData);

        // CREA o AGGIORNA solo se ci sono dati da aggiornare
        $oldChannelName = $userProfile ? $userProfile->channel_name : null;
        Log::info('Vecchio channel_name: ' . ($oldChannelName ?: 'null'));

        try {
            if (!$userProfile) {
                Log::info('UserProfile non esiste, creo un nuovo profilo');
                // Se non esiste un profilo, creane uno nuovo con i dati disponibili
                if (!empty($updateData)) {
                    $updateData['user_id'] = $user->id;
                    Log::info('Creo nuovo UserProfile con dati: ', $updateData);
                    $userProfile = UserProfile::create($updateData);
                    Log::info('Nuovo UserProfile creato con ID: ' . $userProfile->id);
                } else {
                    // Se non ci sono dati, crea almeno un profilo vuoto
                    Log::info('Nessun dato da aggiornare, creo profilo vuoto');
                    $userProfile = UserProfile::create(['user_id' => $user->id]);
                    Log::info('UserProfile vuoto creato con ID: ' . $userProfile->id);
                }
            } else {
                Log::info('UserProfile esiste (ID: ' . $userProfile->id . '), procedo con aggiornamento');
                if (!empty($updateData)) {
                    $result = $userProfile->update($updateData);
                } else {
                }
                $userProfile->refresh();
            }

            $newChannelName = $userProfile->channel_name;
            $redirectUrl = null;

            if ($newChannelName && $newChannelName !== $oldChannelName) {
                $redirectUrl = route('channel.show', $newChannelName);
            } elseif ($newChannelName) {
                $redirectUrl = route('channel.edit', $newChannelName);
            } else {
                $redirectUrl = route('channel.edit');
            }

            // Se la request è AJAX, restituisci JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Canale aggiornato con successo!',
                    'redirect_url' => $redirectUrl,
                    'data' => [
                        'channel_name' => $newChannelName,
                        'username' => $userProfile->username,
                        'channel_description' => $userProfile->channel_description,
                        'avatar_url' => $userProfile->avatar_url,
                        'banner_url' => $userProfile->banner_url,
                    ]
                ]);
            }

            return redirect($redirectUrl)->with('success', 'Canale aggiornato con successo!');
        } catch (\Exception $e) {
            Log::error('Errore durante l\'aggiornamento del canale: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errore durante l\'aggiornamento del canale',
                    'errors' => ['general' => ['Si è verificato un errore. Riprova più tardi.']]
                ], 500);
            }

            return back()->withInput()->withErrors(['general' => 'Si è verificato un errore. Riprova più tardi.']);
        }
    }

    /**
     * Gestisce l'iscrizione a un canale
     */
    public function subscribe($channel_name, Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Devi essere autenticato per iscriverti'], 401);
        }

        // Determina se è un channel_name o un user_id
        if (is_numeric($channel_name)) {
            // Se è numerico, treat as user_id
            $targetUser = User::find($channel_name);
            if (!$targetUser) {
                return response()->json(['error' => 'Utente non trovato'], 404);
            }
        } else {
            // Se è stringa, treat as channel_name
            $UserProfile = UserProfile::where('channel_name', $channel_name)->first();
            if (!$UserProfile) {
                return response()->json(['error' => 'Canale non trovato'], 404);
            }
            $targetUser = $UserProfile->user;
        }

        if (Auth::id() === $targetUser->id) {
            return response()->json(['error' => 'Non puoi iscriverti al tuo canale'], 400);
        }

        $subscription = Subscription::where('subscriber_id', Auth::id())
            ->where('channel_id', $targetUser->id)
            ->first();

        if ($subscription) {
            $subscription->delete();
            $message = 'Iscrizione cancellata';
            $isSubscribed = false;
        } else {
            $newSubscription = Subscription::create([
                'subscriber_id' => Auth::id(),
                'channel_id' => $targetUser->id,
            ]);

            // Invia notifica di nuovo iscritto
            $targetUser->notify(new NewSubscriberNotification(Auth::user()));

            $message = 'Iscrizione effettuata';
            $isSubscribed = true;
        }

        $subscribersCount = $targetUser->subscribers()->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'subscribed' => $isSubscribed,
            'subscribers_count' => $subscribersCount,
        ]);
    }

    /**
     * Ricerca API per i contenuti del canale dell'utente autenticato
     */
    public function channelSearch(Request $request)
    {
        $query = $request->get('q', '');
        $scope = $request->get('scope', 'channel');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $user = Auth::user();
        $results = [];

        // Cerca nei video dell'utente
        $videos = Video::where('user_id', $user->id)
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->select(['id', 'title', 'description', 'thumbnail_url', 'video_url', 'views_count', 'published_at'])
            ->limit(10)
            ->get();

        foreach ($videos as $video) {
            $results[] = [
                'type' => 'video',
                'title' => $video->title,
                'url' => route('videos.show', $video),
                'thumbnail' => $video->thumbnail_url,
                'date' => $video->published_at->diffForHumans(),
                'views' => $video->views_count
            ];
        }

        // Cerca nelle playlist dell'utente (se necessario)
        if (count($results) < 10) {
            $playlists = $user->playlists()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->select(['id', 'title', 'description', 'created_at'])
                ->limit(5)
                ->get();

            foreach ($playlists as $playlist) {
                $results[] = [
                    'type' => 'playlist',
                    'title' => $playlist->title,
                    'url' => route('playlists.show', $playlist),
                    'thumbnail' => null,
                    'date' => $playlist->created_at->diffForHumans(),
                    'views' => null
                ];
            }
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Mostra la lista delle iscrizioni
     */
    public function subscriptions()
    {
        $subscriptions = Auth::user()->subscriptions()
            ->with('userProfile')
            ->get();

        $latestVideos = Video::published()
            ->where('is_reel', false) // Esclude i reel dalla lista delle iscrizioni
            ->whereIn('user_id', $subscriptions->pluck('id'))
            ->orderBy('published_at', 'desc')
            ->limit(12)
            ->get();

        return view('users.subscriptions', compact('subscriptions', 'latestVideos'));
    }

    /**
     * Mostra la cronologia di visione
     */
    public function watchHistory(Request $request)
    {
        $period = $request->get('period', 'all');

        $query = WatchHistory::with('video.user.userProfile')
            ->where('user_id', Auth::id());

        switch ($period) {
            case 'today':
                $query->whereDate('updated_at', today());
                break;
            case 'week':
                $query->where('updated_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('updated_at', '>=', now()->subMonth());
                break;
        }

        $history = $query->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('users.watch-history', compact('history', 'period'));
    }

    /**
     * Pulisce la cronologia di visione
     */
    public function clearHistory(Request $request)
    {
        $period = $request->get('period', 'all');

        $query = WatchHistory::where('user_id', Auth::id());

        switch ($period) {
            case 'today':
                $query->whereDate('updated_at', today());
                break;
            case 'week':
                $query->where('updated_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('updated_at', '>=', now()->subMonth());
                break;
        }

        $query->delete();

        return back()->with('success', 'Cronologia pulita con successo!');
    }

    /**
     * Mostra i video piaciuti
     */
    public function likedVideos()
    {
        $userId = Auth::id();

        // Usa il modello Like con le relazioni Eloquent
        $likedVideos = Like::with(['likeable.user.userProfile'])
            ->where('user_id', $userId)
            ->where('likeable_type', Video::class)
            ->whereHas('likeable', function ($query) {
                $query->where('status', 'published')
                    ->where('is_public', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('users.liked-videos', compact('likedVideos'));
    }

    /**
     * Mostra le playlist dell'utente
     */
    public function playlists()
    {
        $playlists = Auth::user()->playlists()
            ->with(['videos' => function ($query) {
                $query->orderBy('playlist_videos.position', 'asc');
            }])
            ->withCount('videos')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.playlists', compact('playlists'));
    }



    /**
     * Mostra i video da guardare più tardi
     */
    public function watchLater()
    {
        $user = Auth::user();

        // Ottieni i video dalla tabella watch_later
        $watchLaterRecords = WatchLater::where('user_id', $user->id)
            ->with('video.user.userProfile')
            ->orderBy('added_at', 'desc')
            ->get();

        $watchLaterVideos = $watchLaterRecords->pluck('video')->filter();

        return view('users.watch-later', compact('watchLaterVideos'));
    }

    /**
     * Aggiorna la password dell'utente
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verifica la password attuale
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La password attuale non è corretta.']);
        }

        // Aggiorna la password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password aggiornata con successo!');
    }

    /**
     * Aggiorna le informazioni del profilo utente (nome, email)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:user_profiles,username,' . ($user->userProfile->id ?? 'NULL'),
            'bio' => 'nullable|string|max:160',
        ]);

        // Aggiorna l'utente
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Aggiorna il profilo utente se esiste
        if ($user->userProfile) {
            $user->userProfile->update([
                'username' => $request->username,
                'bio' => $request->bio,
            ]);
        } else {
            // Crea il profilo se non esiste
            UserProfile::create([
                'user_id' => $user->id,
                'username' => $request->username,
                'bio' => $request->bio,
            ]);
        }

        // Gestisci upload avatar
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');

            if ($user->userProfile) {
                // Rimuovi avatar precedente
                if ($user->userProfile->avatar_url) {
                    Storage::delete($user->userProfile->avatar_url);
                }
                $user->userProfile->update(['avatar_url' => $path]);
            } else {
                UserProfile::create([
                    'user_id' => $user->id,
                    'avatar_url' => $path,
                    'username' => $request->username,
                    'bio' => $request->bio,
                ]);
            }
        }

        return back()->with('success', 'Profilo aggiornato con successo!');
    }

    /**
     * Aggiorna le preferenze di notifica dell'utente
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $preferences = [
            'email_new_subscribers' => $request->has('email_new_subscribers'),
            'email_video_comments' => $request->has('email_video_comments'),
            'email_platform_updates' => $request->has('email_platform_updates'),
            'push_realtime_notifications' => $request->has('push_realtime_notifications'),
        ];

        // Salva le preferenze di notifica nel database
        UserPreference::setCategoryPreferences($user->id, 'notifications', $preferences);

        return back()->with('success', 'Preferenze notifiche aggiornate con successo!');
    }

    /**
     * Aggiorna le preferenze dell'app
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $preferences = [
            'language' => $request->language,
            'timezone' => $request->timezone,
            'theme' => $request->theme,
        ];

        // Salva le preferenze dell'app nel database
        UserPreference::setCategoryPreferences($user->id, 'app_preferences', $preferences);

        // Applica immediatamente il tema se cambiato
        if ($request->theme) {
            session(['user_theme' => $request->theme]);
        }

        return back()->with('success', 'Preferenze aggiornate con successo!');
    }

    /**
     * Aggiorna le impostazioni di privacy
     */
    public function updatePrivacy(Request $request)
    {
        $user = Auth::user();

        $preferences = [
            'profile_public' => $request->has('profile_public'),
            'show_activity' => $request->has('show_activity'),
            'analytics_privacy' => $request->has('analytics_privacy'),
        ];

        // Salva le impostazioni di privacy nel database
        UserPreference::setCategoryPreferences($user->id, 'privacy', $preferences);

        return back()->with('success', 'Impostazioni privacy aggiornate con successo!');
    }

    /**
     * Mostra le notifiche dell'utente
     */
    public function notifications()
    {
        $user = Auth::user();

        // Carica le notifiche dell'utente
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calcola statistiche aggiuntive
        $todayNotifications = $user->notifications()
            ->whereDate('created_at', today())
            ->count();

        return view('users.notifications', compact('notifications', 'todayNotifications'));
    }

    /**
     * Marca tutte le notifiche come lette
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tutte le notifiche sono state marcate come lette'
            ]);
        }

        return back()->with('success', 'Tutte le notifiche sono state marcate come lette');
    }

    /**
     * Marca una singola notifica come letta (API)
     */
    public function markNotificationAsRead($notificationId, Request $request)
    {
        $user = Auth::user();

        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifica non trovata'
            ], 404);
        }

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifica marcata come letta'
        ]);
    }

    /**
     * Controlla se ci sono nuove notifiche (API)
     */
    public function checkNewNotifications(Request $request)
    {
        $user = Auth::user();
        $lastCheck = $request->get('last_check', now()->subMinutes(5));

        $newNotifications = $user->notifications()
            ->where('created_at', '>', $lastCheck)
            ->where('read_at', null)
            ->count();

        return response()->json([
            'success' => true,
            'hasNew' => $newNotifications > 0,
            'count' => $newNotifications,
            'last_check' => now()->toISOString()
        ]);
    }

    /**
     * Aggiunge un video alla lista "Guarda più tardi"
     */
    public function addToWatchLater(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id'
        ]);

        $user = Auth::user();
        $video = Video::findOrFail($request->video_id);

        // Aggiungi alla tabella watch_later usando il modello WatchLater
        WatchLater::addToWatchLater($user->id, $video->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Video aggiunto alla lista "Guarda più tardi"'
            ]);
        }

        return back()->with('success', 'Video aggiunto alla lista "Guarda più tardi"');
    }

    /**
     * Rimuove un video dalla lista "Guarda più tardi"
     */
    public function removeFromWatchLater($videoId)
    {
        $user = Auth::user();
        $video = Video::findOrFail($videoId);

        // Rimuovi dalla tabella watch_later usando il modello WatchLater
        WatchLater::removeFromWatchLater($user->id, $video->id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Video rimosso dalla lista "Guarda più tardi"'
            ]);
        }

        return back()->with('success', 'Video rimosso dalla lista "Guarda più tardi"');
    }

    /**
     * API endpoint per ottenere dati dinamici del profilo
     */
    public function getProfileData()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $user = Auth::user()->load('userProfile');

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'userProfile' => $user->userProfile ? [
                    'username' => $user->userProfile->username,
                    'bio' => $user->userProfile->bio,
                    'avatar_url' => $user->userProfile->avatar_url,
                    'created_at' => $user->userProfile->created_at,
                ] : null,
                'preferences' => [
                    'theme' => UserPreference::getValue($user->id, 'app_preferences', 'theme', 'dark'),
                    'language' => UserPreference::getValue($user->id, 'app_preferences', 'language', 'it'),
                    'timezone' => UserPreference::getValue($user->id, 'app_preferences', 'timezone', 'Europe/Rome'),
                ],
                'stats' => [
                    'videos_count' => $user->videos()->published()->count() ?? 0,
                    'subscribers_count' => $user->subscribers()->count() ?? 0,
                    'total_views' => $user->videos()->published()->sum('views_count') ?? 0,
                ]
            ]
        ]);
    }

    /**
     * Blocca un utente
     */
    public function blockUser(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $currentUser = Auth::user();
        $userToBlock = User::findOrFail($request->user_id);

        // Non puoi bloccare te stesso
        if ($currentUser->id === $userToBlock->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non puoi bloccare te stesso'
            ], 400);
        }

        // Controlla se l'utente è già bloccato
        $existingBlock = UserPreference::where('user_id', $currentUser->id)
            ->where('category', 'blocked_users')
            ->where('key', 'blocked_user_' . $userToBlock->id)
            ->first();

        if ($existingBlock) {
            return response()->json([
                'success' => false,
                'message' => 'Utente già bloccato'
            ], 400);
        }

        // Blocca l'utente salvando nelle preferenze
        UserPreference::setValue(
            $currentUser->id,
            'blocked_users',
            'blocked_user_' . $userToBlock->id,
            [
                'user_id' => $userToBlock->id,
                'name' => $userToBlock->name,
                'blocked_at' => now()->toISOString()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "{$userToBlock->name} è stato bloccato"
        ]);
    }

    /**
     * Sblocca un utente
     */
    public function unblockUser(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $currentUser = Auth::user();
        $userToUnblock = User::findOrFail($request->user_id);

        // Rimuovi il blocco dalle preferenze
        UserPreference::where('user_id', $currentUser->id)
            ->where('category', 'blocked_users')
            ->where('key', 'blocked_user_' . $userToUnblock->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "{$userToUnblock->name} è stato sbloccato"
        ]);
    }

    /**
     * Ottieni la lista degli utenti bloccati
     */
    public function getBlockedUsers()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Non autorizzato'], 401);
        }

        $currentUser = Auth::user();
        $blockedUsers = UserPreference::where('user_id', $currentUser->id)
            ->where('category', 'blocked_users')
            ->get();

        $users = [];
        foreach ($blockedUsers as $block) {
            $userData = json_decode($block->value, true);
            if ($userData && isset($userData['user_id'])) {
                $user = User::find($userData['user_id']);
                if ($user) {
                    $users[] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar_url' => $user->userProfile?->avatar_url ? asset('storage/' . $user->userProfile->avatar_url) : null,
                        'blocked_at' => $userData['blocked_at'] ?? $block->created_at
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'blocked_users' => $users
        ]);
    }

    /**
     * Ottiene statistiche giornaliere per il grafico analytics
     */
    private function getDailyStatsForChart($userId, $startDate, $endDate)
    {
        $days = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dateString = $current->toDateString();

            $stats = ChannelAnalytics::where('user_id', $userId)
                ->where('date', $dateString)
                ->selectRaw('
                    SUM(views) as views,
                    SUM(likes) as likes,
                    SUM(comments) as comments,
                    SUM(shares) as shares
                ')
                ->first();

            $days[] = [
                'date' => $dateString,
                'views' => $stats->views ?? 0,
                'likes' => $stats->likes ?? 0,
                'comments' => $stats->comments ?? 0,
                'shares' => $stats->shares ?? 0,
            ];

            $current->addDay();
        }

        return $days;
    }

    /**
     * Ottiene gli iscritti recenti del canale
     */
    private function getRecentSubscribers($userId, $limit = 10)
    {
        return Subscription::where('channel_id', $userId)
            ->with(['subscriber.userProfile'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->subscriber->id,
                    'name' => $subscription->subscriber->name,
                    'avatar_url' => $subscription->subscriber->userProfile?->avatar_url ?
                        asset('storage/' . $subscription->subscriber->userProfile->avatar_url) : null,
                    'subscribed_at' => $subscription->created_at,
                    'time_ago' => $subscription->created_at->diffForHumans()
                ];
            });
    }

    /**
     * Ottiene i commenti recenti sui video dell'utente
     */
    private function getRecentComments($userId, $limit = 10)
    {
        return Comment::whereHas('video', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['user.userProfile', 'video'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => Str::limit($comment->content, 100),
                    'user_name' => $comment->user->name,
                    'user_avatar' => $comment->user->userProfile?->avatar_url ?
                        asset('storage/' . $comment->user->userProfile->avatar_url) : null,
                    'video_title' => $comment->video->title,
                    'video_id' => $comment->video->id,
                    'commented_at' => $comment->created_at,
                    'time_ago' => $comment->created_at->diffForHumans()
                ];
            });
    }

    /**
     * Ottiene statistiche generali della community
     */
    private function getCommunityStats($userId)
    {
        $totalSubscribers = Subscription::where('channel_id', $userId)->count();
        $newSubscribersThisMonth = Subscription::where('channel_id', $userId)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $totalComments = Comment::whereHas('video', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('status', 'approved')
            ->count();

        $commentsThisMonth = Comment::whereHas('video', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('status', 'approved')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return [
            'total_subscribers' => $totalSubscribers,
            'new_subscribers_this_month' => $newSubscribersThisMonth,
            'total_comments' => $totalComments,
            'comments_this_month' => $commentsThisMonth,
            'subscriber_growth_rate' => $totalSubscribers > 0 ?
                round(($newSubscribersThisMonth / $totalSubscribers) * 100, 1) : 0,
            'comment_engagement_rate' => $totalSubscribers > 0 ?
                round(($totalComments / $totalSubscribers) * 100, 1) : 0
        ];
    }
}

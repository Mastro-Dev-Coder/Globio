<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CreatorFeedback;
use App\Models\Report;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    /**
     * Dashboard segnalazioni per l'utente
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'sent');

        if ($tab === 'sent') {
            $reports = Report::where('reporter_id', $user->id)
                ->with(['reportedUser', 'video', 'comment', 'admin'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('users.reports-sent', compact('reports'));
        } else {
            $reports = Report::where('reported_user_id', $user->id)
                ->with(['reporter', 'video', 'comment', 'admin'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('users.reports-received', compact('reports'));
        }
    }

    /**
     * Crea una nuova segnalazione - Vista unificata per tutti i tipi
     */
    public function create(Request $request)
    {
        $type = $request->get('type');
        $targetId = $request->get('id');

        $target = null;
        $targetType = '';
        $presetReasons = [];

        if ($type === 'user' && $targetId) {
            $target = User::findOrFail($targetId);
            $targetType = 'utente';
        } elseif ($type === 'video' && $targetId) {
            $target = Video::with('user')->findOrFail($targetId);
            $targetType = 'video';
        } elseif ($type === 'comment' && $targetId) {
            $target = Comment::with('user')->findOrFail($targetId);
            $targetType = 'commento';
        } elseif ($type === 'channel' && $targetId) {
            $target = User::with('userProfile')->findOrFail($targetId);
            $targetType = 'canale';
        }

        if (!$target) {
            return redirect()->back()->with('error', 'Target non trovato');
        }

        // Ottieni le ragioni predefinite per il tipo di target
        $presetReasons = Report::getPresetReasons($type);

        return view('users.report-create', compact('target', 'type', 'targetType', 'presetReasons'));
    }

    /**
     * Salva una nuova segnalazione
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:user,video,comment,channel',
            'target_id' => 'required|integer',
            'report_type' => 'required|in:spam,harassment,copyright,inappropriate_content,fake_information,other,custom',
            'reason' => 'required|string|max:255',
            'custom_reason' => 'nullable|string|max:255|required_if:report_type,custom',
            'description' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Controllo duplicati
        $existingReport = Report::where('reporter_id', $user->id)
            ->where('type', $request->report_type)
            ->where(function ($query) use ($request) {
                if ($request->type === 'user') {
                    $query->where('reported_user_id', $request->target_id)
                          ->whereNull('channel_id')
                          ->whereNull('video_id')
                          ->whereNull('comment_id');
                } elseif ($request->type === 'video') {
                    $query->where('video_id', $request->target_id);
                } elseif ($request->type === 'comment') {
                    $query->where('comment_id', $request->target_id);
                } elseif ($request->type === 'channel') {
                    $query->where('channel_id', $request->target_id);
                }
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->first();

        if ($existingReport) {
            return redirect()->back()->with('error', 'Hai già segnalato questo contenuto di recente. Riprova più tardi.');
        }

        // Determina la ragione effettiva
        $reason = $request->report_type === 'custom' 
            ? $request->custom_reason 
            : $request->reason;

        $description = $request->description;
        $priority = Report::autoClassifyPriority($request->report_type, $reason . ' ' . $description);

        $report = null;

        if ($request->type === 'user') {
            $report = Report::reportUser(
                $user->id,
                $request->target_id,
                $request->report_type,
                $reason,
                $description,
                $priority
            );
        } elseif ($request->type === 'video') {
            $report = Report::reportVideo(
                $user->id,
                $request->target_id,
                $request->report_type,
                $reason,
                $description,
                $priority
            );
        } elseif ($request->type === 'comment') {
            $report = Report::reportComment(
                $user->id,
                $request->target_id,
                $request->report_type,
                $reason,
                $description,
                $priority
            );
        } elseif ($request->type === 'channel') {
            $report = Report::reportChannel(
                $user->id,
                $request->target_id,
                $request->report_type,
                $reason,
                $description,
                $priority
            );
            
            // Aggiorna la custom_reason se presente
            if ($request->report_type === 'custom' && $request->custom_reason) {
                $report->update(['custom_reason' => $request->custom_reason]);
            }
        }

        if ($report) {
            return redirect()->route('reports.index', ['tab' => 'sent'])
                ->with('success', 'Segnalazione inviata con successo. La esamineremo al più presto.');
        } else {
            return redirect()->back()->with('error', 'Errore durante l\'invio della segnalazione.');
        }
    }

    /**
     * Dettagli di una segnalazione
     */
    public function show($id)
    {
        $user = Auth::user();
        $report = Report::where(function ($query) use ($user) {
            $query->where('reporter_id', $user->id)
                ->orWhere('reported_user_id', $user->id)
                ->orWhere('channel_id', $user->id);
        })
            ->with([
                'reporter:id,name,email',
                'reportedUser:id,name,email',
                'video:id,title,user_id',
                'comment:id,content,user_id,video_id',
                'channel:id,name,email',
                'admin:id,name,email'
            ])
            ->findOrFail($id);

        if ($report->reporter_id !== $user->id && 
            $report->reported_user_id !== $user->id && 
            $report->channel_id !== $user->id) {
            abort(403);
        }

        return view('users.report-show', compact('report'));
    }

    /**
     * Ritira una segnalazione (solo il reporter può ritirarla se ancora in pending)
     */
    public function withdraw($id)
    {
        $user = Auth::user();
        $report = Report::where('id', $id)
            ->where('reporter_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $report->update([
            'status' => 'dismissed',
            'admin_notes' => 'Ritirata dall\'utente che l\'ha segnalata',
            'resolved_at' => now()
        ]);

        return redirect()->route('reports.index', ['tab' => 'sent'])
            ->with('success', 'Segnalazione ritirata con successo.');
    }

    /**
     * Dettagli di una segnalazione (vista admin)
     */
    public function adminShow($id)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $report = Report::with([
            'reporter:id,name,email',
            'reportedUser:id,name,email',
            'video:id,title,user_id,description',
            'comment:id,content,user_id,video_id',
            'channel:id,name,email',
            'admin:id,name,email'
        ])->findOrFail($id);

        return view('admin.reports-show', compact('report'));
    }

    /**
     * Assegna una segnalazione a un admin
     */
    public function adminAssign(Request $request, $id)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

        $report = Report::findOrFail($id);
        $admin = User::findOrFail($request->admin_id);

        if (!$admin->is_admin) {
            return response()->json(['success' => false, 'message' => 'L\'utente selezionato non è un amministratore.'], 400);
        }

        $report->assignTo($admin->id);

        return response()->json([
            'success' => true,
            'message' => "Segnalazione assegnata a {$admin->name}"
        ]);
    }

    /**
     * Risolve una segnalazione (admin)
     */
    public function adminResolve(Request $request, $id)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'action' => 'required|in:content_removed,user_warned,user_suspended,user_banned,channel_warned,channel_hidden,false_report,no_action',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $report = Report::findOrFail($id);

        $this->executeResolutionAction($report, $request->action);

        $report->markAsResolved($request->action, $request->admin_notes);

        return response()->json([
            'success' => true,
            'message' => 'Segnalazione risolta con successo'
        ]);
    }

    /**
     * Rigetta una segnalazione (admin)
     */
    public function adminDismiss(Request $request, $id)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $report = Report::findOrFail($id);
        $report->dismiss($request->admin_notes);

        return response()->json([
            'success' => true,
            'message' => 'Segnalazione rigettata'
        ]);
    }

    /**
     * Dashboard admin per le segnalazioni
     */
    public function adminIndex(Request $request)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }

        $status = $request->get('status', 'pending');
        $priority = $request->get('priority');
        $type = $request->get('type');
        $targetType = $request->get('target_type');

        $query = Report::with(['reporter', 'reportedUser', 'video', 'comment', 'channel']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($targetType) {
            $query->withTargetType($targetType);
        }

        $reports = $query->orderByRaw('CASE priority 
            WHEN "urgent" THEN 1 
            WHEN "high" THEN 2 
            WHEN "medium" THEN 3 
            WHEN "low" THEN 4 
            END')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = Report::getStats(now()->subDays(30)->toDateString(), now()->toDateString());

        return view('admin.reports-index', compact('reports', 'stats', 'status', 'priority', 'type', 'targetType'));
    }

    /**
     * Vista creator per le segnalazioni e feedback
     */
    public function creatorReports(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'reports');
        $status = $request->get('status');

        if ($tab === 'feedback') {
            $query = CreatorFeedback::where('creator_id', $user->id)
                ->with(['admin', 'report']);

            if ($status) {
                if ($status === 'unread') {
                    $query->where('is_read', false);
                } elseif ($status === 'read') {
                    $query->where('is_read', true);
                }
            }

            $feedback = $query->orderBy('created_at', 'desc')->paginate(15);
            $totalFeedback = CreatorFeedback::where('creator_id', $user->id)->count();
            $unreadFeedback = CreatorFeedback::where('creator_id', $user->id)->where('is_read', false)->count();

            $totalReports = Report::where('reported_user_id', $user->id)->count();
            $pendingReports = Report::where('reported_user_id', $user->id)->where('status', Report::STATUS_PENDING)->count();

            return view('users.creator-reports', compact(
                'feedback',
                'totalFeedback',
                'unreadFeedback',
                'totalReports',
                'pendingReports',
                'tab',
                'status'
            ));
        } else {
            // Segnalazioni ricevute (video, commenti, canale)
            $query = Report::where('reported_user_id', $user->id)
                ->orWhere('channel_id', $user->id)
                ->with(['reporter', 'video', 'comment', 'admin']);

            if ($status) {
                $query->where('status', $status);
            }

            $reports = $query->orderBy('created_at', 'desc')->paginate(15);
            $totalReports = Report::where('reported_user_id', $user->id)->orWhere('channel_id', $user->id)->count();
            $pendingReports = Report::where('reported_user_id', $user->id)
                ->orWhere('channel_id', $user->id)
                ->where('status', Report::STATUS_PENDING)->count();

            $feedback = CreatorFeedback::where('creator_id', $user->id)
                ->with(['admin', 'report'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            $totalFeedback = CreatorFeedback::where('creator_id', $user->id)->count();
            $unreadFeedback = CreatorFeedback::where('creator_id', $user->id)->where('is_read', false)->count();

            return view('users.creator-reports', compact(
                'reports',
                'feedback',
                'totalReports',
                'pendingReports',
                'totalFeedback',
                'unreadFeedback',
                'tab',
                'status'
            ));
        }
    }

    /**
     * Segna un feedback come letto
     */
    public function markFeedbackAsRead($feedbackId)
    {
        $user = Auth::user();
        $feedback = CreatorFeedback::where('id', $feedbackId)
            ->where('creator_id', $user->id)
            ->firstOrFail();

        $feedback->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Feedback segnato come letto'
        ]);
    }

    /**
     * Segna tutti i feedback come letti
     */
    public function markAllFeedbackAsRead()
    {
        $user = Auth::user();
        $updated = CreatorFeedback::where('creator_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => "{$updated} feedback segnati come letti"
        ]);
    }

    /**
     * Esegue l'azione di risoluzione corrispondente
     */
    private function executeResolutionAction($report, $action)
    {
        switch ($action) {
            case Report::ACTION_CONTENT_REMOVED:
                if ($report->video_id) {
                    Video::where('id', $report->video_id)->update(['status' => 'hidden']);
                } elseif ($report->comment_id) {
                    Comment::where('id', $report->comment_id)->update(['status' => 'hidden']);
                }
                break;

            case Report::ACTION_USER_WARNED:
                // Notification::create([...]);
                break;

            case Report::ACTION_USER_SUSPENDED:
                User::where('id', $report->reported_user_id)->update(['status' => 'suspended']);
                break;

            case Report::ACTION_USER_BANNED:
                User::where('id', $report->reported_user_id)->update(['status' => 'banned']);
                break;

            case Report::ACTION_CHANNEL_WARNED:
                // Notifica al creator del canale
                break;

            case Report::ACTION_CHANNEL_HIDDEN:
                // Nascondi il canale
                User::where('id', $report->channel_id)->update(['status' => 'hidden']);
                break;
        }
    }

    /**
     * API endpoint per creare segnalazioni rapide (modal dei reel/video)
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'target_id' => 'required|integer',
            'target_type' => 'required|in:video,comment,channel',
            'reason' => 'required|in:spam,inappropriate,copyright,harassment,other',
        ]);

        $user = Auth::user();

        // Mappa delle ragioni
        $reportTypeMap = [
            'spam' => 'spam',
            'inappropriate' => 'inappropriate_content',
            'copyright' => 'copyright',
            'harassment' => 'harassment',
            'other' => 'other'
        ];

        $reportType = $reportTypeMap[$request->reason] ?? 'other';

        // Controllo che non stia segnalando se stesso
        if ($request->target_type === 'video') {
            $video = Video::findOrFail($request->target_id);
            if ($video->user_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non puoi segnalare i tuoi stessi video'
                ], 403);
            }
        } elseif ($request->target_type === 'channel') {
            if ($request->target_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non puoi segnalare il tuo stesso canale'
                ], 403);
            }
        } elseif ($request->target_type === 'comment') {
            $comment = Comment::findOrFail($request->target_id);
            if ($comment->user_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non puoi segnalare i tuoi stessi commenti'
                ], 403);
            }
        }

        // Controllo duplicati
        $existingReport = Report::where('reporter_id', $user->id)
            ->where('type', $reportType)
            ->where(function ($query) use ($request) {
                if ($request->target_type === 'video') {
                    $query->where('video_id', $request->target_id);
                } elseif ($request->target_type === 'comment') {
                    $query->where('comment_id', $request->target_id);
                } elseif ($request->target_type === 'channel') {
                    $query->where('channel_id', $request->target_id);
                }
            })
            ->where('created_at', '>', now()->subDays(7))
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Hai già segnalato questo contenuto di recente. Riprova più tardi.'
            ], 429);
        }

        try {
            $report = match ($request->target_type) {
                'video' => Report::reportVideo(
                    $user->id,
                    $request->target_id,
                    $reportType,
                    $request->reason,
                    null
                ),
                'comment' => Report::reportComment(
                    $user->id,
                    $request->target_id,
                    $reportType,
                    $request->reason,
                    null
                ),
                'channel' => Report::reportChannel(
                    $user->id,
                    $request->target_id,
                    $reportType,
                    $request->reason,
                    null
                ),
                default => null,
            };

            return response()->json([
                'success' => true,
                'message' => 'Segnalazione inviata con successo',
                'report_id' => $report->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'invio della segnalazione. Riprova più tardi.'
            ], 500);
        }
    }

    /**
     * API: Ottieni le ragioni predefinite per un tipo di target
     */
    public function apiGetReasons(Request $request)
    {
        $targetType = $request->get('target_type', 'video');
        
        $reasons = Report::getPresetReasons($targetType);
        
        return response()->json([
            'success' => true,
            'reasons' => $reasons
        ]);
    }
}

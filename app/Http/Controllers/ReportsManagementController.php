<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Models\Video;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReportsManagementController extends Controller
{
    /**
     * Dashboard principale delle segnalazioni
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');
        $targetType = $request->get('target_type', 'all');
        $priority = $request->get('priority', 'all');
        $search = $request->get('search');
        $assignedTo = $request->get('assigned_to', 'all');

        $query = Report::with(['reporter', 'reportedUser', 'video', 'comment', 'channel', 'admin']);

        // Filtri
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($targetType !== 'all') {
            $query->withTargetType($targetType);
        }

        if ($priority !== 'all') {
            $query->where('priority', $priority);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('reporter', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('reportedUser', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($assignedTo !== 'all') {
            if ($assignedTo === 'unassigned') {
                $query->whereNull('admin_id');
            } else {
                $query->where('admin_id', $assignedTo);
            }
        }

        // Ordinamento per priorità e data
        $query->orderByRaw('CASE priority 
            WHEN "urgent" THEN 1 
            WHEN "high" THEN 2 
            WHEN "medium" THEN 3 
            WHEN "low" THEN 4 
            END')
            ->orderBy('created_at', 'desc');

        $reports = $query->paginate(20);

        // Statistiche
        $stats = Report::getStats();

        // Admin per assegnazione
        $admins = User::where('role', 'admin')->get();

        // Opzioni per i filtri
        $statusOptions = [
            'all' => 'Tutti gli stati',
            Report::STATUS_PENDING => 'In attesa',
            Report::STATUS_REVIEWED => 'In revisione',
            Report::STATUS_RESOLVED => 'Risolto',
            Report::STATUS_DISMISSED => 'Respinto',
            Report::STATUS_ESCALATED => 'Escalato',
        ];

        $typeOptions = [
            'all' => 'Tutti i tipi',
            Report::TYPE_SPAM => 'Spam',
            Report::TYPE_HARASSMENT => 'Molestie',
            Report::TYPE_COPYRIGHT => 'Copyright',
            Report::TYPE_INAPPROPRIATE_CONTENT => 'Contenuto inappropriato',
            Report::TYPE_FAKE_INFORMATION => 'Informazioni false',
            Report::TYPE_OTHER => 'Altro',
            Report::TYPE_CUSTOM => 'Personalizzato',
        ];

        // Opzioni per tipo di target
        $targetTypeOptions = [
            'all' => 'Tutti i target',
            'user' => 'Profilo Utente',
            'video' => 'Video',
            'comment' => 'Commento',
            'channel' => 'Canale',
        ];

        $priorityOptions = [
            'all' => 'Tutte le priorità',
            Report::PRIORITY_URGENT => 'Urgente',
            Report::PRIORITY_HIGH => 'Alta',
            Report::PRIORITY_MEDIUM => 'Media',
            Report::PRIORITY_LOW => 'Bassa',
        ];

        return view('admin.reports', compact(
            'reports',
            'stats',
            'admins',
            'statusOptions',
            'typeOptions',
            'priorityOptions',
            'targetTypeOptions',
            'status',
            'type',
            'targetType',
            'priority',
            'search',
            'assignedTo'
        ));
    }

    /**
     * Mostra dettagli di una segnalazione
     */
    public function show(Report $report)
    {
        $report->load([
            'reporter.userProfile',
            'reportedUser.userProfile',
            'video.user.userProfile',
            'comment.user.userProfile',
            'channel.userProfile',
            'admin'
        ]);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Assegna una segnalazione a un admin
     */
    public function assign(Report $report, Request $request)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

        $admin = User::findOrFail($request->admin_id);

        if ($admin->role !== 'admin') {
            return back()->withErrors(['error' => 'Puoi assegnare solo ad altri amministratori.']);
        }

        $report->assignTo($admin->id);

        // Invia notifica al creator se la segnalazione riguarda i suoi contenuti
        if ($report->reportedUser) {
            $report->reportedUser->notify(new \App\Notifications\ReportAssignedNotification($report));
        }

        return back()->with('success', 'Segnalazione assegnata con successo a ' . $admin->name);
    }

    /**
     * Risolve una segnalazione
     */
    public function resolve(Report $report, Request $request)
    {
        $request->validate([
            'resolution_action' => 'required|in:' . implode(',', [
                Report::ACTION_CONTENT_REMOVED,
                Report::ACTION_USER_WARNED,
                Report::ACTION_USER_SUSPENDED,
                Report::ACTION_USER_BANNED,
                Report::ACTION_CHANNEL_WARNED,
                Report::ACTION_CHANNEL_HIDDEN,
                Report::ACTION_FALSE_REPORT,
                Report::ACTION_NO_ACTION,
            ]),
            'admin_notes' => 'nullable|string|max:1000',
            'send_feedback' => 'boolean',
            'feedback_message' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Risolvi la segnalazione
            $report->markAsResolved($request->resolution_action, $request->admin_notes);

            // Azioni sul contenuto
            if ($request->resolution_action === Report::ACTION_CONTENT_REMOVED) {
                if ($report->video) {
                    $report->video->update(['status' => 'rejected']);
                } elseif ($report->comment) {
                    $report->comment->update(['status' => 'hidden']);
                }
            }

            // Azioni sull'utente
            if (in_array($request->resolution_action, [
                Report::ACTION_USER_WARNED,
                Report::ACTION_USER_SUSPENDED,
                Report::ACTION_USER_BANNED
            ])) {
                if ($report->reportedUser) {
                    $userStatus = 'active';
                    if ($request->resolution_action === Report::ACTION_USER_SUSPENDED) {
                        $userStatus = 'suspended';
                    } elseif ($request->resolution_action === Report::ACTION_USER_BANNED) {
                        $userStatus = 'banned';
                    }

                    $report->reportedUser->update(['status' => $userStatus]);
                }
            }

            // Azioni sul canale
            if (in_array($request->resolution_action, [
                Report::ACTION_CHANNEL_WARNED,
                Report::ACTION_CHANNEL_HIDDEN
            ])) {
                if ($report->channel_id) {
                    $channelStatus = 'active';
                    if ($request->resolution_action === Report::ACTION_CHANNEL_HIDDEN) {
                        $channelStatus = 'hidden';
                    }
                    User::where('id', $report->channel_id)->update(['status' => $channelStatus]);
                }
            }

            // Invia feedback al creator se richiesto
            if ($request->boolean('send_feedback') && $report->reportedUser) {
                $feedback = \App\Models\CreatorFeedback::create([
                    'creator_id' => $report->reportedUser->id,
                    'admin_id' => Auth::id(),
                    'report_id' => $report->id,
                    'type' => 'report_resolution',
                    'title' => 'Risoluzione segnalazione',
                    'message' => $request->feedback_message ?: 'La segnalazione riguardante i tuoi contenuti è stata risolta.',
                    'is_read' => false,
                ]);

                // Invia notifica
                $report->reportedUser->notify(new \App\Notifications\CreatorFeedbackNotification($feedback));
            }

            DB::commit();

            return back()->with('success', 'Segnalazione risolta con successo.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Errore durante la risoluzione: ' . $e->getMessage()]);
        }
    }

    /**
     * Respingi una segnalazione
     */
    public function dismiss(Report $report, Request $request)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
            'send_feedback' => 'boolean',
            'feedback_message' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $report->dismiss($request->admin_notes);

            // Invia feedback al creator se richiesto
            if ($request->boolean('send_feedback') && $report->reportedUser) {
                $feedback = \App\Models\CreatorFeedback::create([
                    'creator_id' => $report->reportedUser->id,
                    'admin_id' => Auth::id(),
                    'report_id' => $report->id,
                    'type' => 'report_dismissed',
                    'title' => 'Segnalazione respinta',
                    'message' => $request->feedback_message ?: 'La segnalazione riguardante i tuoi contenuti è stata respinta.',
                    'is_read' => false,
                ]);

                $report->reportedUser->notify(new \App\Notifications\CreatorFeedbackNotification($feedback));
            }

            DB::commit();

            return back()->with('success', 'Segnalazione respinta con successo.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Errore durante il rifiuto: ' . $e->getMessage()]);
        }
    }

    /**
     * Escalation di una segnalazione
     */
    public function escalate(Report $report, Request $request)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
            'send_feedback' => 'boolean',
            'feedback_message' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $report->escalate($request->admin_notes);

            // Invia feedback al creator se richiesto
            if ($request->boolean('send_feedback') && $report->reportedUser) {
                $feedback = \App\Models\CreatorFeedback::create([
                    'creator_id' => $report->reportedUser->id,
                    'admin_id' => Auth::id(),
                    'report_id' => $report->id,
                    'type' => 'report_escalated',
                    'title' => 'Segnalazione escalata',
                    'message' => $request->feedback_message ?: 'La segnalazione riguardante i tuoi contenuti è stata escalata.',
                    'is_read' => false,
                ]);

                $report->reportedUser->notify(new \App\Notifications\CreatorFeedbackNotification($feedback));
            }

            DB::commit();

            return back()->with('success', 'Segnalazione escalata con successo.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Errore durante l\'escalation: ' . $e->getMessage()]);
        }
    }

    /**
     * Azioni di massa
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'report_ids' => 'required|array|min:1',
            'report_ids.*' => 'exists:reports,id',
            'action' => 'required|in:assign,resolve,dismiss,escalate'
        ]);

        $reports = Report::whereIn('id', $request->report_ids)->get();
        $successCount = 0;

        foreach ($reports as $report) {
            try {
                switch ($request->action) {
                    case 'assign':
                        if (!$report->admin_id) {
                            $report->assignTo(Auth::id());
                            $successCount++;
                        }
                        break;

                    case 'resolve':
                        if ($report->status === Report::STATUS_PENDING) {
                            $report->markAsResolved(Report::ACTION_NO_ACTION, 'Risolto in massa');
                            $successCount++;
                        }
                        break;

                    case 'dismiss':
                        if ($report->status === Report::STATUS_PENDING) {
                            $report->dismiss('Respinto in massa');
                            $successCount++;
                        }
                        break;
                    
                    case 'escalate':
                        if (in_array($report->status, [Report::STATUS_PENDING, Report::STATUS_REVIEWED])) {
                            $report->escalate('Escalato in massa');
                            $successCount++;
                        }
                        break;
                }
            } catch (\Exception $e) {
                // Continua con gli altri report
                continue;
            }
        }

        $message = $successCount > 0
            ? "Azione completata su {$successCount} segnalazioni."
            : 'Nessuna segnalazione è stata modificata.';

        return back()->with('success', $message);
    }

    /**
     * Statistiche delle segnalazioni
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', 30);
        $startDate = now()->subDays($period)->toDateString();
        $endDate = now()->toDateString();

        $stats = Report::getStats($startDate, $endDate);

        // Grafici per tipo
        $typeDistribution = Report::selectRaw('type, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type')
            ->get();

        // Grafici per priorità
        $priorityDistribution = Report::selectRaw('priority, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('priority')
            ->get();

        // Trend temporale
        $trendData = [];
        $days = now()->diffInDays(Carbon::parse($startDate));

        for ($i = 0; $i <= $days; $i++) {
            $date = Carbon::parse($startDate)->addDays($i)->toDateString();
            $count = Report::whereDate('created_at', $date)->count();
            $trendData[] = [
                'date' => $date,
                'count' => $count
            ];
        }

        return view('admin.reports.statistics', compact(
            'stats',
            'typeDistribution',
            'priorityDistribution',
            'trendData',
            'period'
        ));
    }

    /**
     * Esporta segnalazioni in CSV
     */
    public function export(Request $request)
    {
        $status = $request->get('status', 'all');
        $priority = $request->get('priority', 'all');
        $targetType = $request->get('target_type', 'all');

        $query = Report::with(['reporter', 'reportedUser', 'video', 'comment', 'channel', 'admin']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($priority !== 'all') {
            $query->where('priority', $priority);
        }

        if ($targetType !== 'all') {
            $query->withTargetType($targetType);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        $filename = 'segnalazioni_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($reports) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'ID',
                'Tipo',
                'Stato',
                'Priorità',
                'Segnalato da',
                'Segnalato',
                'Target',
                'Contenuto',
                'Motivo',
                'Assegnato a',
                'Data creazione',
                'Data aggiornamento'
            ]);

            foreach ($reports as $report) {
                $target = '';
                $content = '';
                
                if ($report->video) {
                    $target = 'Video: ' . $report->video->title;
                    $content = $report->video->description ?? 'N/A';
                } elseif ($report->channel) {
                    $target = 'Canale: ' . $report->channel->name;
                } elseif ($report->comment) {
                    $target = 'Commento';
                    $content = $report->comment->content;
                } elseif ($report->reportedUser) {
                    $target = 'Utente: ' . $report->reportedUser->name;
                }

                fputcsv($file, [
                    $report->id,
                    $report->getTypeLabelAttribute(),
                    $report->getStatusLabelAttribute(),
                    $report->getPriorityLabelAttribute(),
                    $report->reporter->name ?? 'N/A',
                    $report->reportedUser->name ?? 'N/A',
                    $target,
                    Str::limit($content, 100),
                    $report->reason,
                    $report->admin->name ?? 'Non assegnato',
                    $report->created_at->format('d/m/Y H:i:s'),
                    $report->updated_at->format('d/m/Y H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Form per inviare segnalazioni ai creator
     */
    public function createCreatorNotification()
    {
        // Ottieni tutti i creator (utenti che hanno caricato almeno un video)
        $creators = User::whereHas('videos')
            ->with('userProfile')
            ->orderBy('name')
            ->get();

        return view('admin.reports.create-notification', compact('creators'));
    }

    /**
     * Invia segnalazione a un creator
     */
    public function sendCreatorNotification(Request $request)
    {
        $request->validate([
            'creator_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'template_type' => 'nullable|in:warning,info,policy_update,feature_announcement,community_guidelines'
        ]);

        $creator = User::findOrFail($request->creator_id);

        // Genera titolo e messaggio predefiniti se è stato selezionato un template
        $title = $request->title;
        $message = $request->message;

        if ($request->template_type) {
            $templates = $this->getNotificationTemplates();
            
            if (isset($templates[$request->template_type])) {
                $template = $templates[$request->template_type];
                $title = $template['title'];
                $message = $template['message'];
                
                // Se il template usa placeholder, sostituiscili
                if (strpos($message, '{creator_name}') !== false) {
                    $message = str_replace('{creator_name}', $creator->name, $message);
                }
                if (strpos($message, '{platform_name}') !== false) {
                    $message = str_replace('{platform_name}', 'Globio', $message);
                }
            }
        }

        // Crea il feedback
        $feedback = \App\Models\CreatorFeedback::createGeneralNotice(
            $creator->id,
            Auth::id(),
            $title,
            $message
        );

        // Invia notifica
        $creator->notify(new \App\Notifications\CreatorFeedbackNotification($feedback));

        return redirect()->route('admin.reports')
            ->with('success', "Segnalazione inviata con successo a {$creator->name}");
    }

    /**
     * Restituisce i template per i messaggi predefiniti
     */
    private function getNotificationTemplates()
    {
        return [
            'warning' => [
                'title' => 'Avviso Importante',
                'message' => 'Ciao {creator_name}, ti scriviamo per informarti di un problema riscontrato con alcuni dei tuoi contenuti. Ti preghiamo di rivedere le nostre linee guida della community e di apportare le modifiche necessarie entro 24 ore. Se hai domande, contatta il nostro team di supporto.'
            ],
            'info' => [
                'title' => 'Informazione Generale',
                'message' => 'Ciao {creator_name}, vogliamo informarti di importanti aggiornamenti alla piattaforma {platform_name}. Ti invitiamo a consultare la sezione novità nel tuo profilo per maggiori dettagli su queste nuove funzionalità.'
            ],
            'policy_update' => [
                'title' => 'Aggiornamento Politiche',
                'message' => 'Ciao {creator_name}, ti scriviamo per informarti di un importante aggiornamento alle nostre politiche della community. Ti preghiamo di leggere attentamente le nuove linee guida e di conformarti a esse. La mancata conformità potrebbe comportare l\'account.'
            ],
            'feature_announcement' => [
                'title' => 'Nuove Funzionalità',
                'message' => 'Ciao {creator_name}, siamo entusiasti di annunciare il lancio di nuove funzionalità sulla piattaforma {platform_name}! Queste nuove caratteristiche ti aiuteranno a creare contenuti ancora migliori e a connetterti con il tuo pubblico. Scopri tutte le novità nella sezione annunci.'
            ],
            'community_guidelines' => [
                'title' => 'Promemoria Linee Guida',
                'message' => 'Ciao {creator_name}, ti ricordiamo l\'importanza di seguire le nostre linee guida della community per mantenere un ambiente sicuro e accogliente per tutti i creator. Ti preghiamo di rivedere periodicamente queste linee guida sul nostro sito.'
            ]
        ];
    }
}

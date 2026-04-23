<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class CommentModerationController extends Controller
{
    /**
     * Approva un commento
     */
    public function approve(Comment $comment)
    {
        try {
            // Verifica che l'utente sia autorizzato (proprietario del video o admin)
            if (!$this->canModerateComment($comment)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato ad approvare questo commento'
                ], 403);
            }

            if ($comment->approve()) {
                Log::info('Comment approved', [
                    'comment_id' => $comment->id,
                    'video_id' => $comment->video_id,
                    'approved_by' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Commento approvato con successo',
                    'comment' => [
                        'id' => $comment->id,
                        'status' => $comment->status
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'approvazione del commento'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error approving comment', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'approvazione del commento'
            ], 500);
        }
    }

    /**
     * Rifiuta un commento
     */
    public function reject(Comment $comment)
    {
        try {
            // Verifica che l'utente sia autorizzato
            if (!$this->canModerateComment($comment)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato a rifiutare questo commento'
                ], 403);
            }

            if ($comment->reject()) {
                Log::info('Comment rejected', [
                    'comment_id' => $comment->id,
                    'video_id' => $comment->video_id,
                    'rejected_by' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Commento rifiutato con successo',
                    'comment' => [
                        'id' => $comment->id,
                        'status' => $comment->status
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Errore durante il rifiuto del commento'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error rejecting comment', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante il rifiuto del commento'
            ], 500);
        }
    }

    /**
     * Nasconde un commento
     */
    public function hide(Comment $comment)
    {
        try {
            // Verifica che l'utente sia autorizzato
            if (!$this->canModerateComment($comment)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato a nascondere questo commento'
                ], 403);
            }

            if ($comment->hide()) {
                Log::info('Comment hidden', [
                    'comment_id' => $comment->id,
                    'video_id' => $comment->video_id,
                    'hidden_by' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Commento nascosto con successo',
                    'comment' => [
                        'id' => $comment->id,
                        'status' => $comment->status
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la sistemazione del commento'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error hiding comment', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la sistemazione del commento'
            ], 500);
        }
    }

    /**
     * Ottiene tutti i commenti pending per un video
     */
    public function pendingComments(Video $video)
    {
        try {
            // Verifica che l'utente sia autorizzato
            if (!Auth::check() || (Auth::id() !== $video->user_id && !Auth::user()->is_admin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato'
                ], 403);
            }

            $pendingComments = Comment::where('video_id', $video->id)
                ->where('status', Comment::STATUS_PENDING)
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'comments' => $pendingComments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                        ],
                        'created_at' => $comment->created_at->diffForHumans(),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching pending comments', [
                'video_id' => $video->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante il recupero dei commenti'
            ], 500);
        }
    }

    /**
     * Verifica se l'utente può moderare un commento
     */
    private function canModerateComment(Comment $comment): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Gli admin possono moderare tutti i commenti
        if (Auth::user()->is_admin) {
            return true;
        }

        // I proprietari del video possono moderare i commenti del loro video
        return $comment->video->user_id === Auth::id();
    }

    /**
     * Bulk approve multiple comments
     */
    public function bulkApprove(Request $request)
    {
        try {
            $request->validate([
                'comment_ids' => 'required|array',
                'comment_ids.*' => 'exists:comments,id'
            ]);

            $approvedCount = 0;
            $errors = [];

            foreach ($request->comment_ids as $commentId) {
                $comment = Comment::find($commentId);
                
                if (!$comment || !$this->canModerateComment($comment)) {
                    $errors[] = "Commento {$commentId}: non autorizzato";
                    continue;
                }

                if ($comment->approve()) {
                    $approvedCount++;
                    Log::info('Comment bulk approved', [
                        'comment_id' => $comment->id,
                        'video_id' => $comment->video_id,
                        'approved_by' => Auth::id()
                    ]);
                } else {
                    $errors[] = "Commento {$commentId}: errore durante l'approvazione";
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$approvedCount} commenti approvati con successo",
                'approved_count' => $approvedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk approving comments', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'approvazione bulk dei commenti'
            ], 500);
        }
    }

    /**
     * Bulk reject multiple comments
     */
    public function bulkReject(Request $request)
    {
        try {
            $request->validate([
                'comment_ids' => 'required|array',
                'comment_ids.*' => 'exists:comments,id'
            ]);

            $rejectedCount = 0;
            $errors = [];

            foreach ($request->comment_ids as $commentId) {
                $comment = Comment::find($commentId);
                
                if (!$comment || !$this->canModerateComment($comment)) {
                    $errors[] = "Commento {$commentId}: non autorizzato";
                    continue;
                }

                if ($comment->reject()) {
                    $rejectedCount++;
                    Log::info('Comment bulk rejected', [
                        'comment_id' => $comment->id,
                        'video_id' => $comment->video_id,
                        'rejected_by' => Auth::id()
                    ]);
                } else {
                    $errors[] = "Commento {$commentId}: errore durante il rifiuto";
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$rejectedCount} commenti rifiutati con successo",
                'rejected_count' => $rejectedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk rejecting comments', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Errore durante il rifiuto bulk dei commenti'
            ], 500);
        }
    }
}
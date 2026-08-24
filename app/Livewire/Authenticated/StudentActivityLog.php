<?php

namespace App\Livewire\Authenticated;

use Livewire\Component;
use App\Models\Attempt;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentActivityLog extends Component
{
    public $perPage = 10;
    public $hasMore = true;

    public function loadMore()
    {
        $this->perPage += 10;
    }

    public function getActivitiesProperty()
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        $activityLog = collect();

        // 1. Demandes de réinitialisation de mot de passe
        try {
            $passwordResets = DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->get();

            foreach ($passwordResets as $reset) {
                $activityLog->push([
                    'id' => 'pwd_reset_' . md5($reset->token ?? $reset->email),
                    'type' => 'password_reset_request',
                    'icon' => 'bi-key-fill',
                    'color' => 'warning',
                    'title' => 'Demande de Réinitialisation de Mot de Passe',
                    'status' => 'Lien Envoyé',
                    'description' => 'Une demande de réinitialisation du mot de passe a été enregistrée pour votre compte.',
                    'date' => Carbon::parse($reset->created_at),
                ]);
            }
        } catch (\Exception $e) {
            // Ignorer si la table n'existe pas ou erreur SQL mineure
        }

        // 2. Connexions (Sessions)
        try {
            $sessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->get();

            foreach ($sessions as $session) {
                $deviceInfo = $this->parseUserAgent($session->user_agent ?? '');
                $ipInfo = !empty($session->ip_address) ? " (IP : {$session->ip_address})" : '';

                $activityLog->push([
                    'id' => 'session_' . $session->id,
                    'type' => 'login',
                    'icon' => 'bi-box-arrow-in-right',
                    'color' => 'info',
                    'title' => 'Connexion au Compte',
                    'status' => 'Session Active',
                    'description' => "Connexion enregistrée via {$deviceInfo}{$ipInfo}",
                    'date' => Carbon::createFromTimestamp($session->last_activity),
                ]);
            }
        } catch (\Exception $e) {
            // Ignorer si la table n'existe pas ou erreur SQL mineure
        }

        // 3. Tentatives d'évaluations
        $attempts = Attempt::where('user_id', $user->id)
            ->with('evaluation')
            ->latest()
            ->get();

        foreach ($attempts as $attempt) {
            $evalTitle = $attempt->evaluation ? $attempt->evaluation->title : 'Évaluation';
            $activityLog->push([
                'id' => 'attempt_' . $attempt->id,
                'type' => 'attempt',
                'icon' => 'bi-journal-check',
                'color' => $attempt->passed ? 'success' : 'danger',
                'title' => 'Évaluation : ' . $evalTitle,
                'status' => $attempt->passed ? 'Réussi' : 'Échoué',
                'description' => "Score : {$attempt->score}/{$attempt->total_points} ({$attempt->pourcentage}%)",
                'date' => $attempt->created_at,
            ]);
        }

        // 4. Souscriptions enregistrées, validées, modifiées & annulées (y compris soft-deleted)
        $allSubs = Subscription::withTrashed()
            ->where('user_id', $user->id)
            ->with(['formation', 'course', 'chapter'])
            ->latest()
            ->get();

        foreach ($allSubs as $sub) {
            $itemTitle = $sub->formation ? $sub->formation->title : ($sub->course ? $sub->course->title : ($sub->chapter ? $sub->chapter->title : 'Souscription'));

            if ($sub->trashed()) {
                // Souscription annulée / supprimée
                $activityLog->push([
                    'id' => 'sub_del_' . $sub->id,
                    'type' => 'subscription_deleted',
                    'icon' => 'bi-trash-fill',
                    'color' => 'danger',
                    'title' => 'Souscription Annulée',
                    'status' => 'Désactivée',
                    'description' => "La souscription pour <strong>{$itemTitle}</strong> a été annulée ou supprimée.",
                    'date' => $sub->deleted_at ?? $sub->updated_at,
                ]);
            } else {
                if ($sub->is_validated) {
                    $activityLog->push([
                        'id' => 'sub_val_' . $sub->id,
                        'type' => 'subscription_validated',
                        'icon' => 'bi-check-circle-fill',
                        'color' => 'success',
                        'title' => 'Souscription Validée',
                        'status' => 'Accès Actif',
                        'description' => "Accès activé pour : <strong>{$itemTitle}</strong> (Durée : {$sub->duration_in_days} jours)",
                        'date' => $sub->created_at,
                    ]);

                    if ($sub->updated_at && $sub->created_at && $sub->updated_at->diffInMinutes($sub->created_at) > 1) {
                        $expiresDateText = $sub->expires_at ? $sub->expires_at->format('d/m/Y') : 'N/A';
                        $priceFormatted = number_format($sub->price, 0, ',', ' ');
                        $activityLog->push([
                            'id' => 'sub_upd_' . $sub->id,
                            'type' => 'subscription_updated',
                            'icon' => 'bi-arrow-repeat',
                            'color' => 'primary',
                            'title' => 'Souscription Modifiée / Prolongée',
                            'status' => 'Mise à jour',
                            'description' => "Validité : <strong>{$sub->duration_in_days} jours</strong> (Expiration : <strong>{$expiresDateText}</strong>) - Prix : <strong>{$priceFormatted} FCFA</strong> - <strong>{$itemTitle}</strong>",
                            'date' => $sub->updated_at,
                        ]);
                    }
                } else {
                    $activityLog->push([
                        'id' => 'sub_cre_' . $sub->id,
                        'type' => 'subscription_created',
                        'icon' => 'bi-credit-card-fill',
                        'color' => 'warning',
                        'title' => 'Souscription Enregistrée',
                        'status' => 'En attente de validation',
                        'description' => "Demande d'accès enregistrée pour : <strong>{$itemTitle}</strong>",
                        'date' => $sub->created_at,
                    ]);
                }
            }
        }

        // 5. Confirmation e-mail
        if ($user->email_verified_at) {
            $activityLog->push([
                'id' => 'email_verif_' . $user->id,
                'type' => 'email_verified',
                'icon' => 'bi-envelope-check-fill',
                'color' => 'info',
                'title' => 'Compte Vérifié',
                'status' => 'E-mail Confirmé',
                'description' => 'Adresse e-mail vérifiée avec succès',
                'date' => $user->email_verified_at,
            ]);
        }

        // 6. Notifications reçues
        $notifications = \App\Models\AppNotification::with('sender')
            ->where(function ($query) use ($user) {
                $query->where('target_type', 'all')
                      ->orWhere('target_type', $user->role)
                      ->orWhere(function ($q) use ($user) {
                          $q->where('target_type', 'user')
                            ->where('target_user_id', $user->id);
                      });
            })
            ->latest()
            ->take(15)
            ->get();

        $readNotificationIds = \App\Models\AppNotificationRead::where('user_id', $user->id)
            ->pluck('notification_id')
            ->toArray();

        foreach ($notifications as $notif) {
            $isRead = in_array($notif->id, $readNotificationIds);
            $activityLog->push([
                'id' => 'notif_' . $notif->id,
                'type' => 'notification',
                'icon' => $notif->is_important ? 'bi-exclamation-circle-fill' : 'bi-bell-fill',
                'color' => $notif->is_important ? 'danger' : 'info',
                'title' => 'Notification : ' . $notif->title,
                'status' => $isRead ? 'Déjà lue' : 'Nouvelle notification',
                'description' => \Illuminate\Support\Str::limit(strip_tags($notif->message), 100),
                'date' => $notif->created_at,
            ]);
        }

        // Tri par date décroissante
        return $activityLog->sortByDesc('date')->values();
    }

    private function parseUserAgent(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Appareil inconnu';
        }

        $browser = 'Navigateur Web';
        if (str_contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Chrome') && !str_contains($userAgent, 'Edg')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgent, 'Edg')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR')) {
            $browser = 'Opera';
        }

        $os = 'Appareil';
        if (str_contains($userAgent, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS')) {
            $os = 'Mac';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            $os = 'iOS';
        } elseif (str_contains($userAgent, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($userAgent, 'Linux')) {
            $os = 'Linux';
        }

        return "{$browser} sur {$os}";
    }

    public function render()
    {
        $allActivities = $this->getActivitiesProperty();
        $totalCount = $allActivities->count();
        $activities = $allActivities->take($this->perPage);
        $this->hasMore = ($this->perPage < $totalCount);

        return view('livewire.authenticated.student-activity-log', [
            'activities' => $activities,
            'totalCount' => $totalCount,
            'hasMore' => $this->hasMore,
        ]);
    }
}


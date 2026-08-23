<?php

namespace App\Livewire\Authenticated;

use Livewire\Component;
use App\Models\Attempt;
use App\Models\Subscription;

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

        // 1. Tentatives d'évaluations
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

        // 2. Souscriptions enregistrées, validées & mises à jour
        $allSubs = Subscription::where('user_id', $user->id)
            ->with(['formation', 'course', 'chapter'])
            ->latest()
            ->get();

        foreach ($allSubs as $sub) {
            $itemTitle = $sub->formation ? $sub->formation->title : ($sub->course ? $sub->course->title : ($sub->chapter ? $sub->chapter->title : 'Souscription'));

            if ($sub->is_validated) {
                $activityLog->push([
                    'id' => 'sub_val_' . $sub->id,
                    'type' => 'subscription_validated',
                    'icon' => 'bi-check-circle-fill',
                    'color' => 'success',
                    'title' => 'Souscription Validée',
                    'status' => 'Accès Actif',
                    'description' => "Accès activé pour : {$itemTitle}",
                    'date' => $sub->created_at,
                ]);

                if ($sub->updated_at && $sub->created_at && $sub->updated_at->diffInMinutes($sub->created_at) > 1) {
                    $expiresDateText = $sub->expires_at ? $sub->expires_at->format('d/m/Y') : 'N/A';
                    $activityLog->push([
                        'id' => 'sub_upd_' . $sub->id,
                        'type' => 'subscription_updated',
                        'icon' => 'bi-arrow-repeat',
                        'color' => 'primary',
                        'title' => 'Souscription Prolongée',
                        'status' => 'Mise à jour',
                        'description' => "Validité : {$sub->duration_in_days} jours (Expiration : {$expiresDateText}) - {$itemTitle}",
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
                    'description' => "Demande d'accès pour : {$itemTitle}",
                    'date' => $sub->created_at,
                ]);
            }
        }

        // 3. Confirmation e-mail
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

        // 4. Notifications reçues
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

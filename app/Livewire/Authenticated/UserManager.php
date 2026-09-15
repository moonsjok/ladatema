<?php

namespace App\Livewire\Authenticated;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\Attempt;
use App\Mail\AccountStatusChangedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class UserManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $activeTab = 'all'; // all, student, employee, admin, disabled, trashed

    // Propriétés pour l'édition de rôle
    public $editingUserId = null;
    public $editingUserRole = 'student';

    // Propriétés pour l'édition complète d'un utilisateur (Dev Only)
    public $editingName = '';
    public $editingNom = '';
    public $editingPrenoms = '';
    public $editingEmail = '';
    public $editingPhoneCall = '';
    public $editingPhoneWhatsapp = '';
    public $editingPassword = '';
    public $editingIsActive = true;

    // Propriétés pour la suppression
    public $confirmingUserId = null;
    public $confirmingUserName = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    /**
     * Bascule l'état d'activation/désactivation du compte utilisateur.
     */
    public function toggleUserStatus($userId)
    {
        $currentUser = auth()->user();
        if ($userId == $currentUser->id) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Action non autorisée',
                'text' => 'Vous ne pouvez pas désactiver votre propre compte.',
            ]);
            return;
        }

        $user = User::withTrashed()->findOrFail($userId);

        // Bloquer l'accès aux comptes 'dev' pour un rôle 'owner'
        if ($user->role === 'dev' && $currentUser->role !== 'dev') {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Action non autorisée',
                'text' => 'Vous n\'avez pas la possibilité de modifier un compte développeur.',
            ]);
            return;
        }

        $newStatus = !$user->is_active;

        $user->is_active = $newStatus;
        $user->save();

        // Envoi de l'e-mail de notification
        try {
            Mail::to($user->email)->send(new AccountStatusChangedMail($user, $newStatus));
        } catch (\Throwable $e) {
            // Ignorer si l'envoi de mail échoue ou si le Mailable est manquant
        }

        $statusText = $newStatus ? 'réactivé' : 'désactivé';
        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Statut Mis à Jour',
            'text' => "Le compte de {$user->name} a été {$statusText} avec succès. Un e-mail d'information lui a été envoyé.",
        ]);
    }

    /**
     * Prépare le modal de modification de rôle.
     */
    public function editRole($userId)
    {
        $currentUser = auth()->user();
        $user = User::withTrashed()->findOrFail($userId);

        // Bloquer si la cible est 'dev' et l'utilisateur connecté est 'owner'
        if ($user->role === 'dev' && $currentUser->role !== 'dev') {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Action non autorisée',
                'text' => 'Vous n\'avez pas la possibilité de modifier un compte développeur.',
            ]);
            return;
        }

        $this->editingUserId = $user->id;
        $this->editingUserRole = $user->role;
    }

    /**
     * Sauvegarde le rôle de l'utilisateur.
     */
    public function saveRole()
    {
        if (!$this->editingUserId) return;

        $currentUser = auth()->user();

        if ($this->editingUserId == $currentUser->id && $currentUser->role === 'dev') {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Action non autorisée',
                'text' => 'Vous ne pouvez pas modifier votre propre rôle de développeur.',
            ]);
            $this->editingUserId = null;
            return;
        }

        $user = User::withTrashed()->findOrFail($this->editingUserId);

        // Bloquer si la cible est 'dev' et l'utilisateur connecté est 'owner'
        if ($user->role === 'dev' && $currentUser->role !== 'dev') {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Action non autorisée',
                'text' => 'Vous n\'avez pas la possibilité de modifier un compte développeur.',
            ]);
            $this->editingUserId = null;
            return;
        }

        // Seul un dev peut attribuer le rôle 'dev'
        if ($this->editingUserRole === 'dev' && $currentUser->role !== 'dev') {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Action non autorisée',
                'text' => 'Seul un développeur peut attribuer le rôle développeur.',
            ]);
            $this->editingUserId = null;
            return;
        }

        $user->role = $this->editingUserRole;
        $user->save();

        $this->editingUserId = null;

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Rôle Mis à Jour',
            'text' => "Le rôle de {$user->name} a été modifié avec succès.",
        ]);
    }

    /**
     * Prépare l'édition complète d'un profil utilisateur (Dev uniquement).
     */
    public function editUser($userId)
    {
        $currentUser = auth()->user();
        if ($currentUser->role !== 'dev') {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Permission refusée',
                'text' => 'Seul un développeur peut modifier directement le profil complet d\'un utilisateur.',
            ]);
            return;
        }

        $user = User::withTrashed()->findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->editingName = $user->name;
        $this->editingNom = $user->nom ?? '';
        $this->editingPrenoms = $user->prenoms ?? '';
        $this->editingEmail = $user->email;
        $this->editingPhoneCall = $user->phone_call ?? '';
        $this->editingPhoneWhatsapp = $user->phone_whatsapp ?? '';
        $this->editingUserRole = $user->role;
        $this->editingIsActive = (bool) $user->is_active;
        $this->editingPassword = '';
    }

    /**
     * Enregistre les modifications complètes du profil utilisateur (Dev uniquement).
     */
    public function saveFullUser()
    {
        $currentUser = auth()->user();
        if ($currentUser->role !== 'dev') {
            return;
        }

        if (!$this->editingUserId) return;

        $user = User::withTrashed()->findOrFail($this->editingUserId);

        $this->validate([
            'editingName' => 'required|string|max:255',
            'editingEmail' => 'required|email|max:255|unique:users,email,' . $user->id,
            'editingUserRole' => 'required|in:student,employee,owner,dev',
        ]);

        $user->name = $this->editingName;
        $user->nom = $this->editingNom;
        $user->prenoms = $this->editingPrenoms;
        $user->email = $this->editingEmail;
        $user->phone_call = $this->editingPhoneCall;
        $user->phone_whatsapp = $this->editingPhoneWhatsapp;
        $user->role = $this->editingUserRole;
        $user->is_active = $this->editingIsActive;

        if (!empty(trim($this->editingPassword))) {
            $user->password = bcrypt(trim($this->editingPassword));
        }

        $user->save();

        $this->editingUserId = null;

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Profil Mis à Jour',
            'text' => "Les informations du compte de {$user->name} ont été enregistrées avec succès.",
        ]);
    }

    /**
     * Exporte la liste actuelle des utilisateurs au format CSV (compatible Excel & UTF-8).
     */
    public function exportCsv()
    {
        $currentUser = auth()->user();
        $isDev = $currentUser && $currentUser->role === 'dev';

        $query = User::withTrashed();

        if (!$isDev) {
            $query->where('role', '!=', 'dev');
        }

        if (!empty(trim($this->search))) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('nom', 'like', $term)
                  ->orWhere('prenoms', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('phone_call', 'like', $term)
                  ->orWhere('phone_whatsapp', 'like', $term);
            });
        }

        switch ($this->activeTab) {
            case 'student':
                $query->where('role', 'student');
                break;
            case 'employee':
                $query->where('role', 'employee');
                break;
            case 'admin':
                if ($isDev) {
                    $query->whereIn('role', ['owner', 'dev']);
                } else {
                    $query->where('role', 'owner');
                }
                break;
            case 'disabled':
                $query->where('is_active', false);
                break;
            case 'trashed':
                $query->onlyTrashed();
                break;
        }

        $users = $query->latest()->get();

        $fileName = 'export_utilisateurs_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // Intégrer le BOM UTF-8 pour ouverture directe dans Excel avec accents corrects
            fputs($file, "\xEF\xBB\xBF");

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Nom Complet',
                'Email',
                'Rôle',
                'Téléphone Appels',
                'Téléphone WhatsApp',
                'Statut',
                'Date d\'inscription'
            ], ';');

            foreach ($users as $u) {
                $roleLabel = match($u->role) {
                    'dev' => 'Développeur',
                    'owner' => 'Administrateur',
                    'employee' => 'Employé',
                    default => 'Étudiant',
                };

                $statutLabel = $u->trashed() ? 'Corbeille' : ($u->is_active ? 'Actif' : 'Désactivé');

                fputcsv($file, [
                    $u->id,
                    $u->name,
                    $u->email,
                    $roleLabel,
                    $u->phone_call ?? '',
                    $u->phone_whatsapp ?? '',
                    $statutLabel,
                    $u->created_at ? $u->created_at->format('d/m/Y H:i') : ''
                ], ';');
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    /**
     * Prépare la confirmation de suppression définitive sans contraintes SQL.
     */
    public function confirmDelete($userId)
    {
        $currentUser = auth()->user();
        if ($currentUser->role !== 'dev') {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Permission refusée',
                'text' => 'Seul le développeur a l\'autorisation de supprimer définitivement un utilisateur.',
            ]);
            return;
        }

        if ($userId == $currentUser->id) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Action non autorisée',
                'text' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ]);
            return;
        }

        $user = User::withTrashed()->findOrFail($userId);
        $this->confirmingUserId = $user->id;
        $this->confirmingUserName = $user->name;
    }

    /**
     * Effectue la suppression définitive sans blocage de contraintes SQL.
     */
    public function forceDeleteUser()
    {
        $currentUser = auth()->user();
        if ($currentUser->role !== 'dev') {
            return;
        }

        if (!$this->confirmingUserId) return;

        $targetUser = User::withTrashed()->find($this->confirmingUserId);
        if (!$targetUser) {
            $this->confirmingUserId = null;
            return;
        }

        try {
            DB::transaction(function () use ($targetUser) {
                // 1. Nettoyage des profils associés
                if ($targetUser->profile) {
                    $targetUser->profile()->delete();
                }
                DB::table('profiles')->where('user_id', $targetUser->id)->delete();

                // 2. Nettoyage des souscriptions (y compris archivées)
                Subscription::withTrashed()->where('user_id', $targetUser->id)->forceDelete();

                // 3. Nettoyage des tentatives et réponses d'examens
                Attempt::where('user_id', $targetUser->id)->delete();
                DB::table('student_answers')->where('user_id', $targetUser->id)->delete();

                // 4. Nettoyage des sessions et réinitialisations de mot de passe
                DB::table('sessions')->where('user_id', $targetUser->id)->delete();
                DB::table('password_reset_tokens')->where('email', $targetUser->email)->delete();

                // 5. Nettoyage des notifications d'application
                DB::table('app_notification_reads')->where('user_id', $targetUser->id)->delete();
                DB::table('app_notifications')
                    ->where('sender_id', $targetUser->id)
                    ->orWhere('target_user_id', $targetUser->id)
                    ->delete();

                // 6. Nettoyage des fichiers multimédias
                try {
                    $targetUser->clearMediaCollection();
                } catch (\Exception $e) {}

                // 7. Suppression définitive du compte utilisateur
                $targetUser->forceDelete();
            });

            $userName = $this->confirmingUserName;
            $this->confirmingUserId = null;
            $this->confirmingUserName = '';

            $this->dispatch('swal:alert', [
                'icon' => 'success',
                'title' => 'Utilisateur Supprimé',
                'text' => "L'utilisateur {$userName} et tout son historique ont été définitivement supprimés sans aucune contrainte SQL.",
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Erreur lors de la suppression',
                'text' => "Une erreur est survenue : " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Restaure un utilisateur corbeille.
     */
    public function restoreUser($userId)
    {
        $currentUser = auth()->user();
        $user = User::withTrashed()->findOrFail($userId);

        if ($user->role === 'dev' && $currentUser->role !== 'dev') {
            $this->dispatch('swal:alert', [
                'icon' => 'error',
                'title' => 'Permission refusée',
                'text' => 'Vous ne pouvez pas restaurer un compte développeur.',
            ]);
            return;
        }

        $user->restore();

        $this->dispatch('swal:alert', [
            'icon' => 'success',
            'title' => 'Utilisateur Restauré',
            'text' => "Le compte de {$user->name} a été restauré avec succès.",
        ]);
    }

    public function render()
    {
        $currentUser = auth()->user();
        $isDev = $currentUser && $currentUser->role === 'dev';

        // Requête de base pour les statistiques : si l'utilisateur est 'owner', exclure les 'dev'
        $baseQuery = function () use ($isDev) {
            $q = User::query();
            if (!$isDev) {
                $q->where('role', '!=', 'dev');
            }
            return $q;
        };

        // Statistiques globales adaptées au rôle
        $totalUsersCount = $baseQuery()->withTrashed()->count();
        $totalStudentsCount = $baseQuery()->where('role', 'student')->count();
        $activeUsersCount = $baseQuery()->where('is_active', true)->count();
        $disabledUsersCount = $baseQuery()->where('is_active', false)->count();

        // Requête principale avec filtres
        $query = User::withTrashed();

        // Si le rôle de l'utilisateur n'est pas 'dev', cacher les comptes 'dev'
        if (!$isDev) {
            $query->where('role', '!=', 'dev');
        }

        // Recherche textuelle
        if (!empty(trim($this->search))) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('nom', 'like', $term)
                  ->orWhere('prenoms', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('phone_call', 'like', $term)
                  ->orWhere('phone_whatsapp', 'like', $term);
            });
        }

        // Filtre par onglet
        switch ($this->activeTab) {
            case 'student':
                $query->where('role', 'student');
                break;
            case 'employee':
                $query->where('role', 'employee');
                break;
            case 'admin':
                if ($isDev) {
                    $query->whereIn('role', ['owner', 'dev']);
                } else {
                    $query->where('role', 'owner');
                }
                break;
            case 'disabled':
                $query->where('is_active', false);
                break;
            case 'trashed':
                $query->onlyTrashed();
                break;
            default:
                // 'all'
                break;
        }

        $users = $query->latest()->paginate(10);

        return view('livewire.authenticated.user-manager', [
            'users' => $users,
            'totalUsersCount' => $totalUsersCount,
            'totalStudentsCount' => $totalStudentsCount,
            'activeUsersCount' => $activeUsersCount,
            'disabledUsersCount' => $disabledUsersCount,
            'isDev' => $isDev,
        ]);
    }
}

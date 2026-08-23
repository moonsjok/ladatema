<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use App\Models\User;
use App\Mail\AppNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AppNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la liste des notifications envoyées et le formulaire d'envoi.
     */
    public function index()
    {
        $authUser = auth()->user();

        if (!in_array($authUser->role, ['dev', 'owner'])) {
            abort(403, 'Accès non autorisé.');
        }

        // Récupérer la liste des utilisateurs actifs pour le ciblage individuel
        $users = User::whereNull('deleted_at')
            ->orderBy('name', 'asc')
            ->get();

        // Récupérer l'historique des notifications
        $notifications = AppNotification::with(['sender', 'targetUser'])
            ->withCount('reads')
            ->latest()
            ->paginate(15);

        return view('authenticated.owners.notifications.index', compact('users', 'notifications', 'authUser'));
    }

    /**
     * Enregistrer et envoyer une nouvelle notification.
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();

        if (!in_array($authUser->role, ['dev', 'owner'])) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|min:3',
            'target_type' => 'required|in:user,owner,student,all',
            'target_user_id' => 'nullable|required_if:target_type,user|exists:users,id',
            'is_important' => 'nullable|boolean',
        ], [
            'target_user_id.required_if' => 'Veuillez sélectionner un destinataire spécifique.',
            'title.required' => 'Le titre est obligatoire.',
            'message.required' => 'Le message est obligatoire.',
        ]);

        // Empêcher l'Owner de cibler spécifiquement le groupe "owner" (réservé au Dev)
        if ($authUser->role === 'owner' && $validated['target_type'] === 'owner') {
            return redirect()->back()
                ->with('error', 'Seul le Développeur peut envoyer des messages ciblant spécifiquement le groupe Propriétaires.')
                ->withInput();
        }

        try {
            $notification = AppNotification::create([
                'sender_id' => $authUser->id,
                'target_type' => $validated['target_type'],
                'target_user_id' => $validated['target_type'] === 'user' ? $validated['target_user_id'] : null,
                'title' => $validated['title'],
                'message' => $validated['message'],
                'is_important' => $request->has('is_important') ? (bool)$request->is_important : false,
            ]);

            // RÈGLE STRICTE : L'envoi par e-mail se fait EXCLUSIVEMENT si adressé à UNE seule personne spécifique (target_type === 'user')
            if ($notification->target_type === 'user' && $notification->target_user_id) {
                $targetUser = User::find($notification->target_user_id);
                if ($targetUser && !empty($targetUser->email)) {
                    try {
                        Mail::to($targetUser->email)->send(new AppNotificationMail($notification, $targetUser));
                    } catch (\Exception $e) {
                        Log::error("Échec de l'envoi de l'e-mail de notification individuelle : " . $e->getMessage());
                    }
                }
            }

            return redirect()->route('app-notifications.index')
                ->with('success', 'Notification créée et publiée avec succès sur le tableau de bord.');
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de la notification : " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la notification : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Marquer une notification comme lue pour l'utilisateur connecté.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $notification = AppNotification::findOrFail($id);

        AppNotificationRead::firstOrCreate([
            'notification_id' => $notification->id,
            'user_id' => $user->id,
        ], [
            'read_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Supprimer une notification envoyée.
     */
    public function destroy(AppNotification $notification)
    {
        $authUser = auth()->user();
        if (!in_array($authUser->role, ['dev', 'owner'])) {
            abort(403, 'Accès non autorisé.');
        }

        $notification->delete();

        return redirect()->route('app-notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }

    /**
     * Afficher toutes les notifications reçues par l'utilisateur connecté (Boîte de réception complète).
     */
    public function myNotifications()
    {
        $user = auth()->user();

        $notifications = AppNotification::with('sender')
            ->where(function ($query) use ($user) {
                $query->where('target_type', 'all')
                      ->orWhere('target_type', $user->role)
                      ->orWhere(function ($q) use ($user) {
                          $q->where('target_type', 'user')
                            ->where('target_user_id', $user->id);
                      });
            })
            ->latest()
            ->paginate(15);

        $readIds = AppNotificationRead::where('user_id', $user->id)
            ->pluck('notification_id')
            ->toArray();

        return view('authenticated.my_notifications', compact('notifications', 'readIds', 'user'));
    }
}

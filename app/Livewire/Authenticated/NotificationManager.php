<?php

namespace App\Livewire\Authenticated;

use Livewire\Component;
use App\Models\AppNotification;
use App\Models\User;
use App\Mail\AppNotificationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationManager extends Component
{
    // Step management (1: Target Selection, 2: Message Content, 3: Review & Send)
    public $step = 1;

    // Form inputs
    public $target_type = 'all'; // all, student, owner, dev, user
    public $target_user_id = null;
    public $searchUser = '';
    public $title = '';
    public $message = '';
    public $is_important = false;
    public $send_email = false;

    // Status feedback
    public $successMessage = null;

    public function mount()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['dev', 'owner'])) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function updatedTargetType($value)
    {
        if ($value !== 'user') {
            $this->target_user_id = null;
            $this->searchUser = '';
        }
    }

    public function selectUser($userId)
    {
        $this->target_user_id = $userId;
    }

    public function clearSelectedUser()
    {
        $this->target_user_id = null;
    }

    public function goToStep($nextStep)
    {
        if ($nextStep === 2) {
            $this->validateStep1();
        } elseif ($nextStep === 3) {
            $this->validateStep2();
        }

        $this->step = $nextStep;
    }

    public function validateStep1()
    {
        $this->validate([
            'target_type' => 'required|in:all,student,owner,dev,user',
            'target_user_id' => 'nullable|required_if:target_type,user|exists:users,id',
        ], [
            'target_type.required' => 'Veuillez sélectionner le type de destinataire.',
            'target_user_id.required_if' => 'Veuillez sélectionner un destinataire spécifique dans la liste.',
        ]);

        $authUser = Auth::user();
        if ($authUser->role === 'owner' && $this->target_type === 'owner') {
            $this->addError('target_type', 'Seul le Développeur peut envoyer des messages au groupe Propriétaires.');
            throw \Illuminate\Validation\ValidationException::withMessages([
                'target_type' => 'Seul le Développeur peut envoyer des messages au groupe Propriétaires.'
            ]);
        }
    }

    public function validateStep2()
    {
        $this->validate([
            'title' => 'required|string|max:255|min:3',
            'message' => 'required|string|min:3',
            'is_important' => 'boolean',
            'send_email' => 'boolean',
        ], [
            'title.required' => 'Le titre du message est obligatoire.',
            'title.min' => 'Le titre doit faire au moins 3 caractères.',
            'message.required' => 'Le contenu du message est obligatoire.',
            'message.min' => 'Le message doit faire au moins 3 caractères.',
        ]);
    }

    public function sendNotification()
    {
        $this->validateStep1();
        $this->validateStep2();

        $authUser = Auth::user();

        try {
            $notification = AppNotification::create([
                'sender_id' => $authUser->id,
                'target_type' => $this->target_type,
                'target_user_id' => $this->target_type === 'user' ? $this->target_user_id : null,
                'title' => $this->title,
                'message' => $this->message,
                'is_important' => (bool)$this->is_important,
            ]);

            // Envoi par e-mail si demandé ou si destinataire spécifique
            if ($this->send_email || ($this->target_type === 'user' && $this->target_user_id)) {
                if ($this->target_type === 'user' && $this->target_user_id) {
                    $targetUser = User::find($this->target_user_id);
                    if ($targetUser && !empty($targetUser->email)) {
                        try {
                            Mail::to($targetUser->email)->send(new AppNotificationMail($notification, $targetUser));
                        } catch (\Exception $e) {
                            Log::error("Échec de l'envoi de l'e-mail de notification individuelle : " . $e->getMessage());
                        }
                    }
                }
            }

            $this->successMessage = 'La notification a été publiée avec succès !';
            $this->resetForm();
            $this->step = 1;
            
            $this->dispatch('notification-sent');
        } catch (\Exception $e) {
            Log::error("Erreur création notification Livewire: " . $e->getMessage());
            $this->addError('send_error', 'Une erreur est survenue lors de l\'envoi de la notification.');
        }
    }

    public function resetForm()
    {
        $this->target_type = 'all';
        $this->target_user_id = null;
        $this->searchUser = '';
        $this->title = '';
        $this->message = '';
        $this->is_important = false;
        $this->send_email = false;
    }

    public function dismissSuccess()
    {
        $this->successMessage = null;
    }

    public function render()
    {
        $searchResults = collect();
        $selectedUser = null;

        if ($this->target_type === 'user') {
            if (!empty($this->searchUser)) {
                $searchResults = User::whereNull('deleted_at')
                    ->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->searchUser . '%')
                          ->orWhere('prenoms', 'like', '%' . $this->searchUser . '%')
                          ->orWhere('nom', 'like', '%' . $this->searchUser . '%')
                          ->orWhere('email', 'like', '%' . $this->searchUser . '%');
                    })
                    ->take(8)
                    ->get();
            }

            if ($this->target_user_id) {
                $selectedUser = User::find($this->target_user_id);
            }
        }

        return view('livewire.authenticated.notification-manager', [
            'searchResults' => $searchResults,
            'selectedUser' => $selectedUser,
            'authUser' => Auth::user(),
        ]);
    }
}

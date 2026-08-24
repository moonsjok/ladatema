<?php

namespace App\Livewire\Authenticated;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Formation;
use App\Models\Course;
use App\Models\Chapter;
use App\Mail\SubscriptionReminder;
use App\Mail\SubscriptionCreatedMail;
use App\Mail\AdminNewSubscriptionMail;
use App\Mail\SubscriptionValidatedMail;
use App\Mail\SubscriptionUpdatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // State / Navigation
    public $activeTab = 'subscriptions'; // 'subscriptions', 'without_subscriptions', 'stats_formations'

    // Filters
    public $search = '';
    public $statusFilter = 'all'; // 'all', 'validated', 'pending', 'expired', 'trashed'
    public $typeFilter = 'all'; // 'all', 'formation', 'course', 'chapter'
    public $formationFilter = '';
    public $perPage = 15;

    // Modals visibility toggles
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showExtendModal = false;
    public $showReminderModal = false;
    public $showBulkDurationModal = false;

    // Form fields - Create
    public $create_user_id = '';
    public $create_type = 'formation';
    public $create_typeid = '';
    public $create_price = 0;
    public $create_duration_in_days = 90;
    public $create_payment_reference = '';
    public $create_is_validated = true;

    // Form fields - Edit
    public $editingSubscriptionId = null;
    public $edit_user_id = '';
    public $edit_type = 'formation';
    public $edit_typeid = '';
    public $edit_price = 0;
    public $edit_duration_in_days = 90;
    public $edit_payment_reference = '';
    public $edit_is_validated = false;

    // Form fields - Extend
    public $extendingSubscriptionId = null;
    public $additional_days = 30;

    // Form fields - Reminder
    public $reminderUserId = null;
    public $reminderUserName = '';
    public $reminderUserEmail = '';
    public $reminderMessage = '';

    // Form fields - Bulk duration
    public $bulk_duration_in_days = 90;
    public $only_without_expiration = false;

    // Listeners / Reset pagination on filter update
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingFormationFilter()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function filterByStatus($status)
    {
        $this->activeTab = 'subscriptions';
        $this->statusFilter = $status;
        $this->resetPage();
    }

    // --- Action: Quick Validate ---
    public function validateSubscription($id)
    {
        try {
            $subscription = Subscription::withTrashed()->findOrFail($id);
            $subscription->update(['is_validated' => true]);

            // Envoi de l'e-mail de validation
            try {
                $subscription->loadMissing(['user', 'formation', 'course', 'chapter']);
                if ($subscription->user && !empty($subscription->user->email)) {
                    Mail::to($subscription->user->email)->send(new SubscriptionValidatedMail($subscription));
                }
            } catch (\Exception $mailEx) {
                Log::error("Erreur d'envoi du mail de validation : " . $mailEx->getMessage());
            }

            session()->flash('success', "La souscription #{$subscription->id} a été validée avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de la validation : " . $e->getMessage());
        }
    }

    // --- Action: Create Subscription ---
    public function openCreateModal($userId = null)
    {
        $this->resetCreateForm();
        if ($userId) {
            $this->create_user_id = $userId;
        }
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    private function resetCreateForm()
    {
        $this->create_user_id = '';
        $this->create_type = 'formation';
        $this->create_typeid = '';
        $this->create_price = 0;
        $this->create_duration_in_days = 90;
        $this->create_payment_reference = '';
        $this->create_is_validated = true;
    }

    public function updatedCreateType()
    {
        $this->create_typeid = '';
        $this->updateCreatePriceDefault();
    }

    public function updatedCreateTypeid()
    {
        $this->updateCreatePriceDefault();
    }

    private function updateCreatePriceDefault()
    {
        if (empty($this->create_typeid)) {
            $this->create_price = 0;
            return;
        }

        if ($this->create_type === 'formation') {
            $item = Formation::find($this->create_typeid);
        } elseif ($this->create_type === 'course') {
            $item = Course::find($this->create_typeid);
        } elseif ($this->create_type === 'chapter') {
            $item = Chapter::find($this->create_typeid);
        }

        if (isset($item) && isset($item->price)) {
            $this->create_price = $item->price;
        }
    }

    public function saveSubscription()
    {
        $this->validate([
            'create_user_id' => 'required|exists:users,id',
            'create_type' => 'required|in:formation,course,chapter',
            'create_typeid' => 'required|integer',
            'create_price' => 'required|integer|min:0',
            'create_duration_in_days' => 'required|integer|min:1|max:365',
            'create_payment_reference' => 'nullable|string|max:255',
        ]);

        try {
            $duration = max(90, (int)$this->create_duration_in_days);
            $subscription = Subscription::create([
                'user_id' => $this->create_user_id,
                'formation_id' => $this->create_type === 'formation' ? $this->create_typeid : null,
                'course_id' => $this->create_type === 'course' ? $this->create_typeid : null,
                'chapter_id' => $this->create_type === 'chapter' ? $this->create_typeid : null,
                'type' => $this->create_type,
                'price' => $this->create_price,
                'duration_in_days' => $duration,
                'expires_at' => now()->addDays($duration),
                'payment_reference' => $this->create_payment_reference,
                'is_validated' => (bool)$this->create_is_validated,
            ]);

            // Notifications e-mail
            try {
                $subscription->loadMissing(['user', 'formation', 'course', 'chapter']);
                if ($subscription->user && !empty($subscription->user->email)) {
                    Mail::to($subscription->user->email)->send(new SubscriptionCreatedMail($subscription));
                    if ($subscription->is_validated) {
                        Mail::to($subscription->user->email)->send(new SubscriptionValidatedMail($subscription));
                    }
                }
                $adminEmail = config('mail.from.address');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new AdminNewSubscriptionMail($subscription));
                }
            } catch (\Exception $mailEx) {
                Log::error("Erreur lors de l'envoi des mails de création : " . $mailEx->getMessage());
            }

            session()->flash('success', "Souscription créée avec succès.");
            $this->closeCreateModal();
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de la création : " . $e->getMessage());
        }
    }

    // --- Action: Edit Subscription ---
    public function openEditModal($id)
    {
        $sub = Subscription::withTrashed()->findOrFail($id);
        $this->editingSubscriptionId = $sub->id;
        $this->edit_user_id = $sub->user_id;
        $this->edit_type = $sub->type;
        $this->edit_typeid = $sub->formation_id ?? ($sub->course_id ?? ($sub->chapter_id ?? ''));
        $this->edit_price = $sub->price;
        $this->edit_duration_in_days = $sub->duration_in_days ?? 90;
        $this->edit_payment_reference = $sub->payment_reference ?? '';
        $this->edit_is_validated = (bool)$sub->is_validated;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingSubscriptionId = null;
    }

    public function updateSubscription()
    {
        $this->validate([
            'edit_user_id' => 'required|exists:users,id',
            'edit_type' => 'required|in:formation,course,chapter',
            'edit_typeid' => 'required|integer',
            'edit_price' => 'required|integer|min:0',
            'edit_duration_in_days' => 'required|integer|min:1|max:365',
            'edit_payment_reference' => 'nullable|string|max:255',
        ]);

        try {
            $subscription = Subscription::withTrashed()->findOrFail($this->editingSubscriptionId);

            $oldDuration = $subscription->duration_in_days;
            $oldValidatedState = (bool)$subscription->is_validated;
            $oldPrice = $subscription->price;

            $duration = max(90, (int)$this->edit_duration_in_days);
            $baseDate = $subscription->created_at ? (clone $subscription->created_at) : now();
            $newExpiresAt = (clone $baseDate)->addDays($duration);

            $subscription->update([
                'user_id' => $this->edit_user_id,
                'formation_id' => $this->edit_type === 'formation' ? $this->edit_typeid : null,
                'course_id' => $this->edit_type === 'course' ? $this->edit_typeid : null,
                'chapter_id' => $this->edit_type === 'chapter' ? $this->edit_typeid : null,
                'type' => $this->edit_type,
                'price' => $this->edit_price,
                'duration_in_days' => $duration,
                'expires_at' => $newExpiresAt,
                'payment_reference' => $this->edit_payment_reference,
                'is_validated' => (bool)$this->edit_is_validated,
            ]);

            // Notification des modifications
            $changes = [];
            if ($oldDuration != $duration) {
                $changes[] = "La durée de validité a été modifiée à <strong>{$duration} jours</strong> (Expiration : <strong>" . $newExpiresAt->format('d/m/Y') . "</strong>).";
            }
            if ($oldPrice != $this->edit_price) {
                $changes[] = "Le prix a été révisé à <strong>" . number_format($this->edit_price, 0, ',', ' ') . " FCFA</strong>.";
            }

            if (!empty($changes)) {
                try {
                    $subscription->loadMissing(['user', 'formation', 'course', 'chapter']);
                    if ($subscription->user && !empty($subscription->user->email)) {
                        Mail::to($subscription->user->email)->send(new SubscriptionUpdatedMail($subscription, $changes));
                    }
                } catch (\Exception $mailEx) {
                    Log::error("Erreur mail mise à jour : " . $mailEx->getMessage());
                }
            }

            session()->flash('success', "Souscription mise à jour avec succès.");
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }

    // --- Action: Extend Duration ---
    public function openExtendModal($id)
    {
        $this->extendingSubscriptionId = $id;
        $this->additional_days = 30;
        $this->showExtendModal = true;
    }

    public function closeExtendModal()
    {
        $this->showExtendModal = false;
        $this->extendingSubscriptionId = null;
    }

    public function saveExtendSubscription()
    {
        $this->validate([
            'additional_days' => 'required|integer|min:1|max:365',
        ]);

        try {
            $subscription = Subscription::withTrashed()->findOrFail($this->extendingSubscriptionId);
            $subscription->extend((int)$this->additional_days);

            session()->flash('success', "Souscription étendue de {$this->additional_days} jours supplémentaires.");
            $this->closeExtendModal();
        } catch (\Exception $e) {
            session()->flash('error', "Erreur d'extension : " . $e->getMessage());
        }
    }

    // --- Action: Delete / Restore ---
    public function deleteSubscription($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->delete();
            session()->flash('success', "Souscription annulée/archivée.");
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'annulation : " . $e->getMessage());
        }
    }

    public function restoreSubscription($id)
    {
        try {
            $subscription = Subscription::withTrashed()->findOrFail($id);
            $subscription->restore();
            session()->flash('success', "Souscription restaurée avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de la restauration : " . $e->getMessage());
        }
    }

    // --- Action: Send Reminder Email ---
    public function openReminderModal($userId)
    {
        $user = User::findOrFail($userId);
        $this->reminderUserId = $user->id;
        $this->reminderUserName = $user->name;
        $this->reminderUserEmail = $user->email;
        $this->reminderMessage = "Bonjour {$user->name},\n\nNous avons remarqué que vous n'avez pas encore souscrit à une formation sur notre plateforme. Découvrez nos cours disponibles dès maintenant pour booster vos compétences !";
        $this->showReminderModal = true;
    }

    public function closeReminderModal()
    {
        $this->showReminderModal = false;
        $this->reminderUserId = null;
    }

    public function sendReminderEmail()
    {
        $this->validate([
            'reminderMessage' => 'required|string|min:10',
        ]);

        try {
            $student = User::findOrFail($this->reminderUserId);
            if (empty($student->email)) {
                session()->flash('error', "Cet étudiant n'a pas d'adresse e-mail valide.");
                return;
            }

            Mail::to($student->email)->send(new SubscriptionReminder($this->reminderMessage, $student));

            session()->flash('success', "E-mail de relance envoyé avec succès à {$student->email}.");
            $this->closeReminderModal();
        } catch (\Exception $e) {
            session()->flash('error', "Erreur d'envoi du mail de relance : " . $e->getMessage());
        }
    }

    // --- Action: Bulk Duration Update ---
    public function openBulkDurationModal()
    {
        $this->bulk_duration_in_days = 90;
        $this->only_without_expiration = false;
        $this->showBulkDurationModal = true;
    }

    public function closeBulkDurationModal()
    {
        $this->showBulkDurationModal = false;
    }

    public function saveBulkDuration()
    {
        $this->validate([
            'bulk_duration_in_days' => 'required|integer|min:90|max:365',
        ]);

        try {
            $query = Subscription::query();
            $duration = max(90, (int)$this->bulk_duration_in_days);

            if ($this->only_without_expiration) {
                $query->whereNull('expires_at');
            }

            $subscriptions = $query->get();
            $updatedCount = 0;

            foreach ($subscriptions as $subscription) {
                $subscription->duration_in_days = $duration;
                $baseDate = $subscription->created_at ? (clone $subscription->created_at) : now();
                $subscription->expires_at = (clone $baseDate)->addDays($duration);
                $subscription->save();
                $updatedCount++;
            }

            session()->flash('success', "{$updatedCount} souscription(s) mise(s) à jour avec une durée de {$duration} jours.");
            $this->closeBulkDurationModal();
        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de la mise à jour groupée : " . $e->getMessage());
        }
    }

    public function render()
    {
        // --- KPI Counts ---
        $totalSubscriptions = Subscription::whereNull('deleted_at')->count();
        $pendingSubscriptionsCount = Subscription::whereNull('deleted_at')->where('is_validated', false)->count();

        $studentsWithCount = Subscription::whereNull('subscriptions.deleted_at')
            ->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->distinct('user_id')
            ->count('user_id');

        $studentsWithoutCount = User::where('role', 'student')
            ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('subscriptions')
                    ->whereRaw('subscriptions.user_id = users.id')
                    ->whereNull('subscriptions.deleted_at');
            })
            ->count();

        // --- Data Query: Tab 1 - Subscriptions ---
        $subscriptions = collect();
        if ($this->activeTab === 'subscriptions') {
            $query = Subscription::withTrashed()->with(['user', 'formation', 'course', 'chapter']);

            // Filter: Status
            if ($this->statusFilter === 'validated') {
                $query->whereNull('deleted_at')->where('is_validated', true);
            } elseif ($this->statusFilter === 'pending') {
                $query->whereNull('deleted_at')->where('is_validated', false);
            } elseif ($this->statusFilter === 'expired') {
                $query->whereNull('deleted_at')->where('expires_at', '<', now());
            } elseif ($this->statusFilter === 'trashed') {
                $query->onlyTrashed();
            }

            // Filter: Type
            if ($this->typeFilter !== 'all') {
                $query->where('type', $this->typeFilter);
            }

            // Filter: Formation
            if (!empty($this->formationFilter)) {
                $query->where('formation_id', $this->formationFilter);
            }

            // Filter: Search
            if (!empty(trim($this->search))) {
                $term = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('payment_reference', 'like', $term)
                      ->orWhereHas('user', function ($uQuery) use ($term) {
                          $uQuery->where('name', 'like', $term)
                                 ->orWhere('email', 'like', $term)
                                 ->orWhere('prenoms', 'like', $term)
                                 ->orWhere('nom', 'like', $term);
                      });
                });
            }

            $subscriptions = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        }

        // --- Data Query: Tab 2 - Students Without Subscriptions ---
        $studentsWithout = collect();
        if ($this->activeTab === 'without_subscriptions') {
            $query = User::where('role', 'student')
                ->whereNull('deleted_at')
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('subscriptions')
                        ->whereRaw('subscriptions.user_id = users.id')
                        ->whereNull('subscriptions.deleted_at');
                });

            if (!empty(trim($this->search))) {
                $term = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('prenoms', 'like', $term)
                      ->orWhere('nom', 'like', $term)
                      ->orWhere('phone_call', 'like', $term)
                      ->orWhere('phone_whatsapp', 'like', $term);
                });
            }

            $studentsWithout = $query->orderBy('name', 'asc')->paginate($this->perPage);
        }

        // --- Data Query: Tab 3 - Stats Per Formation ---
        $statsFormations = collect();
        if ($this->activeTab === 'stats_formations') {
            $statsFormations = Subscription::join('formations', 'formations.id', '=', 'subscriptions.formation_id')
                ->whereNull('subscriptions.deleted_at')
                ->whereNotNull('subscriptions.formation_id')
                ->whereHas('user', function ($q) {
                    $q->whereNull('deleted_at');
                })
                ->groupBy('subscriptions.formation_id', 'formations.title')
                ->select(
                    'subscriptions.formation_id',
                    DB::raw('formations.title as formation_title'),
                    DB::raw('COUNT(*) as total_subscriptions'),
                    DB::raw('COUNT(DISTINCT subscriptions.user_id) as unique_users')
                )
                ->get();
        }

        // Dropdown options
        $studentsList = User::where('role', 'student')->whereNull('deleted_at')->orderBy('name')->get();
        $formationsList = Formation::orderBy('title')->get();
        $coursesList = Course::orderBy('title')->get();
        $chaptersList = Chapter::orderBy('title')->get();

        return view('livewire.authenticated.subscription-manager', [
            'totalSubscriptions' => $totalSubscriptions,
            'pendingSubscriptionsCount' => $pendingSubscriptionsCount,
            'studentsWithCount' => $studentsWithCount,
            'studentsWithoutCount' => $studentsWithoutCount,
            'subscriptions' => $subscriptions,
            'studentsWithout' => $studentsWithout,
            'statsFormations' => $statsFormations,
            'studentsList' => $studentsList,
            'formationsList' => $formationsList,
            'coursesList' => $coursesList,
            'chaptersList' => $chaptersList,
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;

class StudentSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $users = [];
    public $loading = false;
    public $selectedUser = null;
    public $selectedSubscription = null;

    protected $listeners = ['userSelected' => 'selectUser'];

    public function mount()
    {
        $this->search = '';
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 3) {
            $this->users = [];
            return;
        }

        $this->loading = true;
        
        try {
            $users = User::where('role', 'student')
                ->where(function($query) {
                    $query->where('email', 'LIKE', "%{$this->search}%");
                })
                ->with(['souscriptions' => function($query) {
                    $query->with(['formation', 'course', 'chapter']);
                }])
                ->limit(10)
                ->get();

            $this->users = $users;
            
            Log::info('Recherche Livewire étudiant: ' . $this->search, [
                'results_count' => $users->count(), 
                'users' => $users->pluck('email')->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur recherche Livewire: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->dispatch('showUpdateModal', [
            'user' => $this->selectedUser,
            'subscriptions' => $this->selectedUser->souscriptions
        ]);
    }

    public function render()
    {
        return view('livewire.admin.student-search');
    }
}

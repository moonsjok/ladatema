<?php

namespace App\Livewire\Authenticated;

use Livewire\Component;
use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use Illuminate\Support\Facades\Auth;

class TopNotifications extends Component
{
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        if ($user) {
            AppNotificationRead::firstOrCreate([
                'notification_id' => $notificationId,
                'user_id' => $user->id,
            ]);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $notifications = collect();

        if ($user) {
            $notifications = AppNotification::with('sender')
                ->where(function ($query) use ($user) {
                    $query->where('target_type', 'all')
                          ->orWhere('target_type', $user->role)
                          ->orWhere(function ($q) use ($user) {
                              $q->where('target_type', 'user')
                                ->where('target_user_id', $user->id);
                          });
                })
                ->whereDoesntHave('reads', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->latest()
                ->get();
        }

        return view('livewire.authenticated.top-notifications', [
            'notifications' => $notifications,
        ]);
    }
}

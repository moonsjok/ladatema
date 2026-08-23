<?php

namespace App\Livewire\Authenticated;

use Livewire\Component;
use App\Models\AppNotification;
use App\Models\AppNotificationRead;
use Illuminate\Support\Facades\Auth;

class HeaderNotificationBell extends Component
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

    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $unread = AppNotification::where(function ($query) use ($user) {
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
            ->get();

        foreach ($unread as $notif) {
            AppNotificationRead::firstOrCreate([
                'notification_id' => $notif->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $notifications = collect();
        $unreadCount = 0;

        if ($user) {
            $allNotifications = AppNotification::with('sender')
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

            $readNotificationIds = AppNotificationRead::where('user_id', $user->id)
                ->pluck('notification_id')
                ->toArray();

            $notifications = $allNotifications->map(function ($notif) use ($readNotificationIds) {
                $notif->is_read = in_array($notif->id, $readNotificationIds);
                return $notif;
            });

            $unreadCount = $notifications->where('is_read', false)->count();
        }

        return view('livewire.authenticated.header-notification-bell', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}

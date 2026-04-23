<?php

namespace App\Listeners;

use App\Models\UserOnlineSession;
use Illuminate\Auth\Events\Logout;

class EndUserSession
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $session = UserOnlineSession::where('user_id', $event->user->id)
                ->whereNull('logout_at')
                ->whereDate('date', now()->toDateString())
                ->first();

            if ($session) {
                $session->endSession();
            }
        }
    }
}

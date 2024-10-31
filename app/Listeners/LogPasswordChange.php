<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use App\Models\PasswordChangeLog;

class LogPasswordChange
{
    public function handle(PasswordChanged $event): void
    {
        PasswordChangeLog::create([
            'user_id' => $event->user->id,
            'ip_address' => $event->ipAddress,
            'user_agent' => $event->userAgent,
            'changed_at' => now(),
        ]);
    }
}

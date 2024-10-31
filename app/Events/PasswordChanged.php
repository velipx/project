<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordChanged
{
    use Dispatchable, SerializesModels;

    public $user;
    public $ipAddress;
    public $userAgent;

    public function __construct(User $user, $ipAddress, $userAgent)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }
}

<?php

namespace App\Policies\V1;

use App\Models\Tickets;
use App\Models\User;

class TicketPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Tickets $ticket) {
        return $user->id === $ticket->users_id;
    }
}

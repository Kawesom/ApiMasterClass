<?php

namespace App\Policies\V1;

use App\Models\Tickets;
use App\Models\User;
use App\Permissions\V1\Abilities;

class TicketPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function store(User $user, Tickets $ticket) {
        if($user->tokenCan(Abilities::CreateTicket)) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Tickets $ticket) {
        if($user->tokenCan(Abilities::DeleteTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::DeleteOwnTicket)) {
            return $user->id === $ticket->users_id;
        }
        return false;
    }

    public function replace(User $user, Tickets $ticket) {
        if($user->tokenCan(Abilities::ReplaceTicket)) {
            return true;
        }
        return false;
    }

    public function update(User $user, Tickets $ticket) {
        if($user->tokenCan(Abilities::UpdateTicket)) {
            return true;
        } else if ($user->tokenCan(Abilities::UpdateOwnTicket)) {
            return $user->id === $ticket->users_id;
        }
        return false;
    }
}

<?php

namespace App\Repositories\Contracts;

interface InvitationContract extends BaseContract
{
    public function addUserToTeam($team, $user_id);
    public function removeUserFromTeam($team, $user_id);
}

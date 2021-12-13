<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Repositories\Contracts\InvitationContract;

class InvitationRepository extends BaseRepository implements InvitationContract
{
    public function model()
    {
        return Invitation::class;
    }

    public function addUserToTeam($team, $user_id)
    {
        return $team->members()->attach($user_id);
    }

    public function removeUserFromTeam($team, $user_id)
    {
        return $team->members()->detach($user_id);
    }
}

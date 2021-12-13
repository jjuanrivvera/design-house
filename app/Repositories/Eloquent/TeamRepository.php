<?php

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Repositories\Contracts\TeamContract;

class TeamRepository extends BaseRepository implements TeamContract
{
    public function model()
    {
        return Team::class;
    }

    public function fetchUserTeams()
    {
        return auth()->user()->teams;
    }
}

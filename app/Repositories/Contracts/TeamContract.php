<?php

namespace App\Repositories\Contracts;

interface TeamContract extends BaseContract
{
    public function fetchUserTeams();
}

<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserContract extends BaseContract
{
    public function findByEmail($email);

    public function search(Request $request);
}

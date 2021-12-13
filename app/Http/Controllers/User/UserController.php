<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserContract;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class UserController extends Controller
{
    protected $user;

    public function __construct(UserContract $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $users = $this->user->withCriteria([
            new EagerLoad(['designs'])
        ])->all();

        return UserResource::collection($users);
    }

    public function search(Request $request)
    {
        $designers = $this->user->search($request);
        return UserResource::collection($designers);
    }

    public function findByUsername($username)
    {
        $user = $this->user->findWhereFirst('username', $username);
        return new UserResource($user);
    }
}

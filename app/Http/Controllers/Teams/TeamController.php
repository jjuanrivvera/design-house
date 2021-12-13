<?php

namespace App\Http\Controllers\Teams;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\TeamContract;
use App\Repositories\Contracts\UserContract;
use App\Repositories\Contracts\InvitationContract;

class TeamController extends Controller
{
    protected $team;
    protected $user;
    protected $invitation;

    public function __construct(
        TeamContract $team,
        UserContract $user,
        InvitationContract $invitation
    ) {
        $this->team = $team;
        $this->user = $user;
        $this->invitation = $invitation;
    }

    public function index()
    {
        //
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name'],
        ]);

        $team = $this->team->create([
            'name' => $request->name,
            'owner_id' => auth()->id(),
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }

    public function show($id)
    {
        return new TeamResource($this->team->find($id));
    }

    public function update(Request $request, $id)
    {
        $team = $this->team->find($id);

        $this->authorize('update', $team);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,' . $team->id],
        ]);

        $team = $this->team->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }

    public function destroy()
    {
        $team = $this->team->find($id);
        $this->authorize('delete', $team);

        $team->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }

    public function removeFromTeam($teamId, $userId)
    {
        // get the team
        $team = $this->team->find($teamId);
        $user = $this->user->find($userId);

        // check that the user is not the owner
        if ($user->isOwnerOfTeam($team)) {
            return response()->json([
                'message' => 'You are the team owner'
            ], 401);
        }

        // check that the person sending the request
        // is either the owner of the team or the person
        // who wants to leave the team
        if (
            !auth()->user()->isOwnerOfTeam($team) &&
            auth()->id() !== $user->id
        ) {
            return response()->json([
                'message' => 'You cannot do this'
            ], 401);
        }

        $this->invitation->removeUserFromTeam($team, $userId);

        return response()->json(['message' => 'Success'], 200);
    }

    public function fetchUserTeams()
    {
        $teams = $this->team->fetchUserTeams();

        return TeamResource::collection($teams);
    }
}

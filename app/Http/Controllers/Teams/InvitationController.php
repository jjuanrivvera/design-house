<?php

namespace App\Http\Controllers\Teams;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Mail\InvitationToJoinTeam;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Contracts\TeamContract;
use App\Repositories\Contracts\UserContract;
use App\Repositories\Contracts\InvitationContract;

class InvitationController extends Controller
{
    protected $invitation;

    protected $team;

    protected $user;

    public function __construct(
        InvitationContract $invitation,
        TeamContract $team,
        UserContract $user
    ) {
        $this->invitation = $invitation;
        $this->team = $team;
        $this->user = $user;
    }

    public function invite(Request $request, $teamId)
    {
        $team = $this->team->find($teamId);

        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        $user = auth()->user();

        if (! $user->isOwnerOfTeam($team)) {
            return response()->json([
                'error' => 'You are not the owner of the team'
            ], 401);
        }

        if ($team->hasPendingInvite($request->email)) {
            return response()->json([
                'error' => 'Email already has a pending invite'
            ], 422);
        }

        $recipient = $this->user->findByEmail($request->email);

        if (! $recipient) {
            $this->createInvitation($team, $request->email, false);

            return response()->json([
                'message' => 'Invitation sent'
            ]);
        }

        if ($team->hasUser($recipient)) {
            return response()->json([
                'error' => 'User already in the team'
            ], 422);
        }

        $this->createInvitation($team, $recipient->email);

        return response()->json([
            'message' => 'Invitation sent'
        ]);
    }

    public function resend($id)
    {
        $invitation = $this->invitation->find($id);

        if (! auth()->user()->isOwnerOfTeam($invitation->team)) {
            return response()->json([
                'error' => 'You are not the owner of the team'
            ], 401);
        }

        $recipient = $this->user->findByEmail($invitation->recipient_email);

        Mail::to($invitation->recipient_email)->send(new InvitationToJoinTeam($invitation, (bool) $recipient));

        return response()->json([
            'message' => 'Invitation resent'
        ]);
    }

    public function respond(Request $request, $id)
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required']
        ]);

        $token = $request->token;
        $decision = $request->decision; // 'accept' or 'deny'
        $invitation = $this->invitation->find($id);

        // dd(auth()->user()->email == $invitation->recipient_email);

        // check if the invitation belongs to this user
        $this->authorize('respond', $invitation);

        // check to make sure that the tokens match
        if ($invitation->token !== $token) {
            return response()->json([
                'message' => 'Invalid Token'
            ], 401);
        }

        // check if accepted
        if ($decision !== 'deny') {
            $this->invitation->addUserToTeam($invitation->team, auth()->id());
        }

        $invitation->delete();

        return response()->json(['message' => 'Successful'], 200);
    }

    public function destroy($id)
    {
        $invitation = $this->invitation->find($id);
        $this->authorize('delete', $invitation);

        $invitation->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }

    protected function createInvitation(Team $team, string $email, $userExists = true)
    {
        $invitation = $this->invitation->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(rand(), true))
        ]);

        Mail::to($email)->send(new InvitationToJoinTeam($invitation, $userExists));
    }
}

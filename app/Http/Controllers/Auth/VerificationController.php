<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Providers\RouteServiceProvider;
use App\Repositories\Contracts\UserContract;
use Illuminate\Foundation\Auth\VerifiesEmails;

/**
 * Class VerificationController
 * @package App\Http\Controllers\Auth
 */
class VerificationController extends Controller
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserContract $user)
    {
        // $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->user = $user;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, User $user)
    {
        // check if the url is valid signed url
        if (!URL::hasValidSignature($request)) {
            return response()->json(["errors" => [
                "message" => "Invalid validation link or signature"
            ]], 422);
        }

        // check if the user has already verified account
        if ($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            "message" => "Email successfully verified"
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

        $user = $this->user->findWhereFirst('email', $request->email);

        if (!$user) {
            return response()->json(["errors" => [
                "email" => 'No User could be found with this email address'
            ]], 422);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => 'Verification link resent'
        ]);
    }
}

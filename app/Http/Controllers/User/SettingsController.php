<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Rules\CheckSamePassword;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class SettingsController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'tagline' => 'required|max:255',
            'name' => 'required|max:255',
            'about' => 'required|min:20|string',
            'formatted_address' => 'required|max:255',
            'location.latitude' => 'required|numeric|min:-90|max:90',
            'location.longitude' => 'required|numeric|min:-180|max:180',
        ]);

        $location = new Point($request->location['latitude'], $request->location['longitude']);

        $user->update([
            'name' => $request->name,
            'tagline' => $request->tagline,
            'about' => $request->about,
            'available_to_hire' => $request->available_to_hire,
            'formatted_address' => $request->formatted_address,
            'location' => $location,
        ]);

        return new UserResource($user);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => ['required', new MatchOldPassword()],
            'password' => ['required','confirmed','min:6', new CheckSamePassword()],
        ]);

        $user = auth()->user();

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
        ], 200);
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\Contracts\UserContract;

class UserRepository extends BaseRepository implements UserContract
{
    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();

        //onlu users who has designers
        if ($request->has_designs) {
            $query->has('designs');
        }

        //check avaivable to hier
        if ($request->available_to_hire) {
            $query->where('available_to_hire', true);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $distance = $request->distance;
        $unit = $request->unit;

        if ($latitude && $longitude) {
            $point = new Point($latitude, $longitude);
            $unit = 'km' ? $distance *= 1000 : $distance *= 1609.34;
            $query->distanceSphereExcludingSelf('location', $point, $distance);
        }

        if ($request->orderByLatest) {
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query->get();
    }
}

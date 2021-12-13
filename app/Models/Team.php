<?php

namespace App\Models;

use App\Models\User;
use App\Models\Design;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // when team is created, add current user as member
        static::created(function ($team) {
            $team->members()->attach(auth()->user()->id);
        });

        static::deleting(function ($team) {
            $team->members()->sync([]);
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function invitations()
    {
        return $this->hasOne(Invitation::class);
    }

    public function hasPendingInvite($email)
    {
        return  (bool) $this->invitations()
            ->where('recipient_email', $email)
            ->count();
    }

    public function hasUser(User $user)
    {
        return $this->members()
            ->where('team_user.id', $user->id)
            ->exists();
    }
}

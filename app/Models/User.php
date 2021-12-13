<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;
    use SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tagline',
        'about',
        'username',
        'location',
        'formated_address',
        'available_to_hire'
    ];

    /**
     * The attributes that are spatial.
     *
     * @var array
     */
    protected $spatialFields = [
        'location'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'photo_url'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getPhotoUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . 'jpg?size=200&d=mm';
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function designs()
    {
        return $this->hasMany('App\Models\Design');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function teams()
    {
        return $this->belongsToMany('App\Models\Team')->withTimestamps();
    }

    public function invitations()
    {
        return $this->hasMany('App\Models\Invitation', 'recipient_email', 'email');
    }

    public function chats()
    {
        return $this->belongsToMany('App\Models\Chat', 'participants')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Message');
    }

    public function ownedTeams()
    {
        return $this->teams()->where('owner_id', $this->id);
    }

    public function isOwnerOfTeam($team)
    {
        return (bool) $this->teams()
            ->where('teams.id', $team->id)
            ->where('owner_id', $this->id)
            ->count();
    }

    public function getChatWithUser($userId)
    {
        $chat = $this->chats()->whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->first();

        return $chat;
    }

    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }
}

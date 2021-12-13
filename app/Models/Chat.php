<?php

namespace App\Models;

use App\Models\User;
use App\Models\Message;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $guarded = [];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function isUnReadForUser(User $user)
    {
        return (bool) $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '!=', $user->id)
            ->count();
    }

    public function markAsReadForUser(User $user): void
    {
        $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '!=', $user->id)
            ->update([
                'last_read' => now()
            ]);
    }
}

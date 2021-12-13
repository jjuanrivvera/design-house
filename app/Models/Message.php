<?php

namespace App\Models;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $touches = ['chat'];

    protected $guarded = [];

    public function getBodyAttribute($value)
    {
        if (!auth()->check()) {
            return null;
        }

        if ($this->trashed()) {
            if (auth()->id() == $this->sender->id) {
                return  'You deleted this message';
            } else {
                return "{$this->sender->name} deleted this message";
            }
        }

        return $value;
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

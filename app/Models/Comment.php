<?php

namespace App\Models;

use App\Models\User;
use App\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use Likeable;

    protected $guarded = [];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

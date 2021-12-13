<?php

namespace App\Models\Traits;

use App\Models\Like;
use App\Models\User;

trait Likeable
{
    public static function bootLikeable()
    {
        static::deleting(function($model) {
            $model->removeLikes();
        });
    }

    public function removeLikes()
    {
        if ($this->likes()->count()) {
            $this->likes()->delete();
        }
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        if (! auth()->check()) {
            return false;
        }

        if ($this->isLikedByUser(auth()->user())) {
            return false;
        }

        $this->likes()->create([
            'user_id' => auth()->id(),
        ]);
    }

    public function unlike()
    {
        if (! auth()->check()) {
            return false;
        }

        if (! $this->isLikedByUser(auth()->user())) {
            return false;
        }

        $this->likes()->where('user_id', auth()->id())->delete();
    }

    public function isLikedByUser(User $user)
    {
        return (bool) $this->likes()->where('user_id', $user->id)->count();
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
use Illuminate\Http\Request;
use App\Repositories\Contracts\DesignContract;

class DesignRepository extends BaseRepository implements DesignContract
{
    public function model()
    {
        return Design::class;
    }

    public function applyTags($id, array $tags)
    {
        $design = $this->find($id);
        $design->retag($tags);
    }

    public function addComment($designId, array $comment)
    {
        $design = $this->find($designId);
        return $design->comments()->create($comment);
    }

    public function like($id)
    {
        $design = $this->find($id);

        if ($design->isLikedByUser(auth()->user())) {
            $design->unlike();
        } else {
            $design->like();
        }
    }

    public function isLikedByUser($id)
    {
        $design = $this->find($id);

        return $design->isLikedByUser(auth()->user());
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();
        $query->where('is_live', true);

        // Return with commments only
        if ($request->has_comments) {
            $query->has('comments');
        }

        // Return with Team only
        if ($request->has_team) {
            $query->has('team');
        }

        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        } else {
            $query->latest();
        }

        if ($request->orderBy == 'likes') {
            $query->withCount('likes')->orderByDesc('likes_count');
            // ->orderByDesc('likes_count');
        }

        return $query->get();
    }
}

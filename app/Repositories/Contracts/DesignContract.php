<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface DesignContract extends BaseContract
{
    public function applyTags($id, array $tags);
    public function addComment($designId, array $comment);
    public function like($id);
    public function isLikedByUser($id);
    public function search(Request $request);
}

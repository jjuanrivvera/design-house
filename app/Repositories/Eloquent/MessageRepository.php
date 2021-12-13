<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Contracts\MessageContract;

class MessageRepository extends BaseRepository implements MessageContract
{
    public function model()
    {
        return Message::class;
    }
}

<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class LatestFirst implements CriterionInterface
{
    public function apply($model)
    {
        return $model->orderBy('created_at', 'desc');
    }
}

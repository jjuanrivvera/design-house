<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\CriterionInterface;

class WithTrashed implements CriterionInterface
{
    public function apply($model)
    {
        return $model->withTrashed();
    }
}

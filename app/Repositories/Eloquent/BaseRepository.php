<?php

namespace App\Repositories\Eloquent;

// use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\BaseContract;

abstract class BaseRepository implements BaseContract
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    protected function getModelClass()
    {
        if (! method_exists($this, 'model')) {
            throw new \Exception('Method model() is not defined in ' . get_class($this));
        }

        return app()->make($this->model());
    }

    public function all()
    {
        return $this->model->all();
    }
}

<?php

namespace App\Repositories\Eloquent;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Repositories\Contracts\BaseContract;
use App\Repositories\Criteria\CriteriaInterface;

/**
 * Class BaseRepository
 * @package App\Repositories\Eloquent
 */
abstract class BaseRepository implements BaseContract, CriteriaInterface
{
    /**
     * @var mixed
     */
    protected $model;

    /**
     * BaseRepository constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getModelClass()
    {
        if (! method_exists($this, 'model')) {
            throw new \Exception('Method model() is not defined in ' . get_class($this));
        }

        return app()->make($this->model());
    }

    public function withCriteria(...$criteria)
    {
        $criteria = Arr::flatten($criteria);

        foreach ($criteria as $criterion) {
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->model->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function findWhere(string $column, string $value)
    {
        return $this->model->where($column, $value)->get();
    }

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function findWhereFirst(string $column, string $value)
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    /**
     * @param int $perPage
     * @param string $column
     * @param string $order
     * @return mixed
     */
    public function paginate(int $perPage = 15, string $column = 'id', string $order = 'DESC')
    {
        return $this->model->orderBy($column, $order)->paginate($perPage);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        $model = $this->find($id);
        $model->update($data);

        return $model;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $model = $this->find($id);
        return $model->delete();
    }
}

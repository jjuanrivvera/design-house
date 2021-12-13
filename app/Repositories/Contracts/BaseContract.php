<?php

namespace App\Repositories\Contracts;

/**
 * Interface BaseContract
 * @package App\Repositories\Contracts
 */
interface BaseContract
{
    /**
     * @return mixed
     */
    public function all();

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id);

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function findWhere(string $column, string $value);

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function findWhereFirst(string $column, string $value);

    /**
     * @param int $perPage
     * @param string $column
     * @param string $order
     * @return mixed
     */
    public function paginate(int $perPage = 15, string $column = 'id', string $order = 'DESC');

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);
}

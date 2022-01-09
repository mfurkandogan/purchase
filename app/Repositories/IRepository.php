<?php

namespace App\Repositories;

interface IRepository
{
    /**
     * @param array $columns
     * @return mixed
     */
    public function get($columns = array('*'));

    /**
     * @param object $data
     * @return mixed
     */
    public function create(object $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function upsert(array $data, $key);

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);


    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = array('*'));

    /**
     * @param array $attributes
     * @param null $orderBy
     * @param string $sortOrder
     * @param array $with
     * @param string[] $columns
     * @return mixed
     */
    public function getManyByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc', $with = [], $columns = array("*"));
}

<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseRepository implements IRepository
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function get($columns = array('*'))
    {
        try {
            return $this->model->get($columns);
        } catch (\Exception $exception) {
            return false;
        }
    }


    /**
     * @param array $dataArray
     * @param $key
     * @return string
     */
    public function upsert(array $dataArray, $key)
    {
        DB::beginTransaction();

        try {
            $this->model->upsert($dataArray,$key);
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param object|array $data
     * @return false|mixed
     */
    public function create( $data)
    {
        DB::beginTransaction();

        if(!is_array($data)){
            $data = $data->toArray();
        }

        try {
            $createdItem = $this->model->create($data);
            DB::commit();
            return $createdItem->id;

        } catch (\Exception $exception) {
            DB::rollback();
           // return false;
            return $exception->getMessage();

        }
    }

    /**
     * @param array $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if (!$row) {
            return false;
        }
        DB::beginTransaction();
        try {
            $row->fill($data)->save();
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollback();
            return false;
        }
    }


    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $this->model->destroy($id);
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollback();
            return false;
        }

    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = array('*'))
    {
        try {
            return $this->model->find($id);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param array $attributes
     * @param null $orderBy
     * @param string $sortOrder
     * @param array $with
     * @param array $columns
     * @return mixed
     */
    public function getManyByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc', $with = [], $columns = array("*"))
    {
        $query = $this->queryBuilder($attributes, $orderBy, $sortOrder);

        if (!empty($with)) {
            foreach ($with as $relation) {
                $query = $query->with($relation);
            }
        }

        try {
            return $query->get($columns);
        } catch (\Exception $exception) {
           return false;
        }
    }

    /**
     * @param array $attributes
     * @param null $orderBy
     * @param string $sortOrder
     * @return mixed
     */
    protected function queryBuilder(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $query = $this->model->query();

        foreach ($attributes as $field => $value) {
            if ($field == 'where_in') {
                $query = $query->whereIn(key($value), $value[key($value)]);
            } else if ($field == 'whereJsonContains') {
                foreach ($value as $col => $json) {
                    $query = $query->whereJsonContains($col, $json);
                }
            } else if ($field == 'limit') {
                $query = $query->limit($value);
            } else if ($field == 'offset') {
                $query = $query->offset($value);
            } else if ($field == 'whereHas') {
                foreach ($value as $first => $relations) {
                    foreach ($relations as $col => $like) {
                        $query = $query->whereHas($first, function ($q) use ($col, $like) {
                            $q->where($col, $like);
                        });
                    }
                }
            } else if ($field == 'whereHasNot') {
                foreach ($value as $first => $relations) {
                    foreach ($relations as $col => $like) {
                        $query = $query->whereHas($first, function ($q) use ($col, $like) {
                            $q->where($col, "!=", $like);
                        });
                    }
                }
            } else if ($field == 'whereHasLike') {
                foreach ($value as $first => $relations) {
                    foreach ($relations as $col => $like) {
                        $query = $query->whereHas($first, function ($q) use ($col, $like) {
                            $q->where($col, 'ILIKE', '%' . $like . '%');
                        });
                    }
                }
            } else if ($field == 'whereHasIn') {
                foreach ($value as $first => $relations) {
                    foreach ($relations as $col => $in) {
                        $query = $query->whereHas($first, function ($q) use ($col, $in) {
                            $q->whereIn($col, $in);
                        });
                    }
                }
            } else if ($field == 'like') {
                foreach ($value as $col => $like) {
                    $query = $query->where($col, 'LIKE', $like);
                }
            } else if ($field == 'not') {
                foreach ($value as $col => $not) {
                    $query = $query->where($col, '!=', $not);
                }
            } else if ($field == 'between') {
                $query = $query->whereBetween(key($value), $value[key($value)]);
            } else {
                $query = $query->where($field, $value);
            }
        }

        if (null !== $orderBy) {
            if (!is_array($orderBy)) {
                $orderBy = [$orderBy];
            }

            foreach ($orderBy as $order) {
                $query->orderBy($order, $sortOrder);
            }
        }

        return $query;
    }

}

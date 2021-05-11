<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Work in progress...
*/
abstract class CrudRepository
{
    /** @var Model */
    private $model;

    /**
     * CrudRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

     public function getModel() : Model
     {
         return $this->model;
     }
}

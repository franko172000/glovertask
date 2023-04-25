<?php

namespace App\Persistence\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function getModel(): Model{
        return $this->model;
    }
}

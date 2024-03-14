<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ComponnetRepository;
use App\Entities\Componnet;
use App\Validators\ComponnetValidator;

/**
 * Class ComponnetRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ComponnetRepositoryEloquent extends BaseRepository implements ComponnetRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Componnet::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}

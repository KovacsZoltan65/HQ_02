<?php

namespace App\Repositories;

//use Prettus\Repository\Eloquent\BaseRepository;
//use Prettus\Repository\Criteria\RequestCriteria;
//use App\Repositories\RoleRepository;
//use App\Entities\Role;
//use App\Validators\RoleValidator;

/**
 * Class RoleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RoleRepository extends BaseRepository implements \App\Interfaces\RoleRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return \App\Models\Role::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(\App\Criteria\RoleCriteria::class));
    }
    
}

<?php

namespace App\Repositories;

//use Prettus\Repository\Eloquent\BaseRepository;
//use Prettus\Repository\Criteria\RequestCriteria;
//use App\Repositories\PermissionRepository;
//use App\Entities\Permission;
//use App\Validators\PermissionValidator;

/**
 * Class PermissionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PermissionRepository extends BaseRepository implements \App\Interfaces\PermissionRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return \App\Models\Permission::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(\App\Criteria\PermissionCriteria::class));
    }
    
}

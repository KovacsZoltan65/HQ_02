<?php

namespace App\Repositories;

use App\Criteria\ComponentCriteria;
use App\Interfaces\ComponentRepositoryInterface;
use App\Models\Componnet;

/**
 * Class ComponnetRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ComponentRepository extends BaseRepository implements ComponentRepositoryInterface {
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model() {
        return Componnet::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot() {
        $this->pushCriteria(app( ComponentCriteria::class ));
    }
    
}

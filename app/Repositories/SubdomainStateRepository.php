<?php

namespace App\Repositories;

use App\Criteria\SubdomainStateCriteria;
use App\Interfaces\SubdomainStateRepositoryInterface;
use App\Models\SubdomainState;

/**
 * Class SubdomainStateRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SubdomainStateRepository extends BaseRepository implements SubdomainStateRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SubdomainState::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(SubdomainStateCriteria::class));
    }
    
}

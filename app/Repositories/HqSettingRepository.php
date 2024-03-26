<?php

namespace App\Repositories;

use App\Criteria\HqSettingCriteria;
use App\Interfaces\HqSettingRepositoryInterface;
use App\Models\HqSetting;

/**
 * Class HqSettingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class HqSettingRepository extends BaseRepository implements HqSettingRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return HqSetting::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(HqSettingCriteria::class));
    }
    
}

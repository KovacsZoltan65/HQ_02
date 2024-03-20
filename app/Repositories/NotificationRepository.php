<?php

namespace App\Repositories;

use App\Criteria\NotificationCriteria;
use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;

/**
 * Class NotificationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Notification::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(NotificationCriteria::class));
    }
    
}

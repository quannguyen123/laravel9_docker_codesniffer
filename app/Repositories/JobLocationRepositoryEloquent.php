<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\JobLocationRepository;
use App\Models\JobLocation;
use App\Validators\JobLocationValidator;

/**
 * Class JobLocationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class JobLocationRepositoryEloquent extends BaseRepository implements JobLocationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return JobLocation::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}

<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AlertJobRepository;
use App\Models\AlertJob;
use App\Validators\AlertJobValidator;

/**
 * Class AlertJobRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AlertJobRepositoryEloquent extends BaseRepository implements AlertJobRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AlertJob::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}

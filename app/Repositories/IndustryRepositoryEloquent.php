<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\industryRepository;
use App\Models\Industry;
use App\Validators\IndustryValidator;

/**
 * Class IndustryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class IndustryRepositoryEloquent extends BaseRepository implements IndustryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Industry::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}

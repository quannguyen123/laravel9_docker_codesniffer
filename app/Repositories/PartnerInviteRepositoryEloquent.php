<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PartnerInviteRepository;
use App\Models\PartnerInvite;
use App\Validators\PartnerInviteValidator;

/**
 * Class PartnerInviteRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PartnerInviteRepositoryEloquent extends BaseRepository implements PartnerInviteRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PartnerInvite::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}

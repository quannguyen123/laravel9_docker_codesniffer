<?php

namespace App\Repositories;

use App\Models\User;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function search(array $filter): LengthAwarePaginator
    {
        $orderBy = Arr::get($filter, 'orderBy', '');
        $orderType = Arr::get($filter, 'orderType', '');
        
        /** @var Builder $this */
        if (in_array($orderType, ['asc', 'desc']) && in_array($orderBy, ['name', 'email', 'created_at'])) {
            $query = $this->orderBy((string)$orderBy, (string)$orderType);
        } else {
            $query = $this->orderBy('id', 'asc');
        }

        if (!empty($filter['search'])) {
            $query = $query->where(function ($query) use ($filter) {
                $query->where('email', 'LIKE', '%'.$filter['search'].'%')
                ->orWhere('name', 'LIKE', '%'.$filter['search'].'%');
            });
        }
        $query->where('type', config('custom.user-type.type-user'));

        $limit = config('custom.paginate');

        if (!empty(Cookie::get('limit')) && in_array(Cookie::get('limit'), (array)config('custom.page-limit'))) {
            $limit = Cookie::get('limit');
        }

        return $query->paginate($limit);
    }
}

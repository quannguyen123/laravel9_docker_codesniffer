<?php

namespace App\Repositories;

use App\Models\User;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
// use App\Entities\User;
use App\Validators\UserValidator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

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
            // $query = $query->where(function ($query) use ($filter) {
            //     $query->where('email', 'LIKE', '%'.$filter['search'].'%')
            //     ->orWhere(DB::raw("CONCAT(first_name,' ',last_name)"), 'LIKE', '%'.$filter['search'].'%')
            //     ->orWhere(DB::raw("CONCAT(first_name_hira,' ',last_name_hira)"), 'LIKE', '%'.$filter['search'].'%');
            // });

            $query = $query->where(function ($query) use ($filter) {
                $query->where('email', 'LIKE', '%'.$filter['search'].'%')
                ->orWhere('name', 'LIKE', '%'.$filter['search'].'%');
            });
        }

        $limit = 2;
        // if (isset($_COOKIE['limit']) && in_array($_COOKIE['limit'], (array)config('custom.page-limit'))) {
        //     $limit = $_COOKIE['limit'];
        // }

        return $query->paginate($limit);
    }
}

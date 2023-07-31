<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\JobRepository;
use App\Models\Job;
use App\Validators\JobValidator;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

/**
 * Class JobRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class JobRepositoryEloquent extends BaseRepository implements JobRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Job::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
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
                $query->where('name', 'LIKE', '%'.$filter['search'].'%');
            });
        }

        $limit = config('custom.paginate');

        if (!empty(Cookie::get('limit')) && in_array(Cookie::get('limit'), (array)config('custom.page-limit'))) {
            $limit = Cookie::get('limit');
        }

        return $query->paginate($limit);
    }

    public function getJobByStatus($request): LengthAwarePaginator
    {
        $orderBy = Arr::get($request, 'orderBy', '');
        $orderType = Arr::get($request, 'orderType', '');
        
        /** @var Builder $this */
        if (in_array($orderType, ['asc', 'desc']) && in_array($orderBy, ['name', 'email', 'created_at'])) {
            $query = $this->orderBy((string)$orderBy, (string)$orderType);
        } else {
            $query = $this->orderBy('id', 'asc');
        }

        if (!empty($request['search'])) {
            $query = $query->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%'.$request['search'].'%');
            });
        }

        if (!empty($request['filters']['occupation'])) {
            $query->leftJoin('job_occupation', 'job_occupation.job_id', '=', 'jobs.id');
            $query->where('job_occupation.occupation_id', $request['filters']['occupation']);
        }

        if (!empty($request['filters']['location'])) {
            $query->leftJoin('job_location', 'job_location.job_id', '=', 'jobs.id');
            $query->leftJoin('company_location', 'company_location.id', '=', 'job_location.company_location_id');
            $query->where('company_location.province_id', $request['filters']['location']);
        }

        if (!empty($request['filters']['created_by'])) {
            $query->where('created_by', $request['filters']['created_by']);
        }

        $query = $query->where('company_id', Auth::guard('api-user')->user()->company[0]['id']);
        
        if (!empty($filter['status'])) {
            $status = Arr::get($filter, 'status', '');

            switch($status) {
                case 'public':
                    $query = $query->where('status', config('custom.job-status.public'))
                                ->where('expiration_date', '>=', Carbon::today());
                    break;
    
                // Việc bị ẩn do thay đổi trạng thái
                case 'hidden':
                    $query = $query->where('status', config('custom.job-status.hidden'))
                            ->where('expiration_date', '>=', Carbon::today());
                    break;
                    
                // Sắp hết hạn trong 7 ngày
                case 'about_to_expire':
                    $query = $query->where('status', config('custom.job-status.public'))
                                ->whereBetween('expiration_date', [Carbon::today(), Carbon::today()->addDays(7)]);
                    break;
    
                // Việc làm hết hạn
                case 'expired':
                    $query = $query->where(function ($query) {
                        $query->where('status', config('custom.job-status.expired'))
                            ->orWhere('expiration_date', '<', Carbon::today());
                    });
                    break;
    
                // Việc làm nháp
                case 'draft':
                    $query = $query->where('status', config('custom.job-status.draft'));
                    break;
    
                // Việc làm ảo
                case 'virtual':
                    $query = $query->where('status', config('custom.job-status.virtual'));
                    break;
            }
        }
        
        $limit = config('custom.paginate');

        if (!empty(Cookie::get('limit')) && in_array(Cookie::get('limit'), (array)config('custom.page-limit'))) {
            $limit = Cookie::get('limit');
        }

        dd($query->toSql());
        return $query->paginate($limit);
    }
}

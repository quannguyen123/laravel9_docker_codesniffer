<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderRepository;
use App\Models\Order;
use App\Validators\OrderValidator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderRepositoryEloquent extends BaseRepository implements OrderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Order::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getOrder($request): LengthAwarePaginator
    {
        $orderBy = Arr::get($request, 'orderBy', '');
        $orderType = Arr::get($request, 'orderType', '');
        
        /** @var Builder $this */
        if (in_array($orderType, ['asc', 'desc']) && in_array($orderBy, ['name', 'email', 'created_at'])) {
            $query = $this->orderBy((string)$orderBy, (string)$orderType);
        } else {
            $query = $this->orderBy('id', 'desc');
        }

        $query->leftJoin('companies', 'companies.id', '=', 'orders.company_id');
        $query->leftJoin('order_detail', 'order_detail.order_id', '=', 'orders.id');
        $query->leftJoin('services', 'services.id', '=', 'order_detail.service_id');

        if (!empty($request['filters']['company_id'])) {
            $query->where('orders.company_id', $request->filters['company_id']);
        }

        if (!empty($request['filters']['service_id'])) {
            $query->where('order_detail.service_id',  $request->filters['service_id']);
        }

        if (!empty( $request->filters['payment_status'])) {
            $query->where('orders.payment_status',  $request->filters['payment_status']);
        }

        $query->groupBy('orders.id');
        $query->select(
            'orders.id',
            'orders.total',
            'orders.user_id',
            'orders.company_id',
            'orders.payment_status',
            'orders.payment_date',
            'orders.payment_transaction',
            'orders.payment_response_code',

            'companies.name as company_name',
            DB::raw("GROUP_CONCAT(services.name SEPARATOR ', ') as `services_name`")
        );     
        
        $limit = config('custom.paginate');

        if (!empty(Cookie::get('limit')) && in_array(Cookie::get('limit'), (array)config('custom.page-limit'))) {
            $limit = Cookie::get('limit');
        }

        return $query->paginate($limit);
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Order.
 *
 * @package namespace App\Models;
 */
class Order extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total',
        'user_id',
        'user_id',
        'company_id',
        'company_id',
        'payment_status',
        'payment_date',
        'payment_transaction',
        'payment_response_code',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    
    public function orderDetail() {
        return $this->belongsToMany(Service::class, 'order_detail', 'order_id', 'service_id')
        ->withPivot('price', 'count', 'total', 'used_time');
    }

    public function company() {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

}

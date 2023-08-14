<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderDetail.
 *
 * @package namespace App\Models;
 */
class OrderDetail extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'order_detail';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'service_id',
        'price',
        'count',
        'total',
        'used_time',
    ];

    public function service() {
        return $this->belongsTo('App\Models\Service', 'service_id', 'id')->select('id', 'name', 'type', 'price', 'used_time', 'description', 'content');
    }
}

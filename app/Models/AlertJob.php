<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AlertJob.
 *
 * @package namespace App\Models;
 */
class AlertJob extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'position',
        'salary_min',
        'rank',
        'province',
        'occupation',
        'industry',
        'interval',
        'notification_by',
        'status'
    ];

    protected $casts = [
        'rank' => Json::class,
        'province' => Json::class,
        'occupation' => Json::class,
        'industry' => Json::class,
   ];

}

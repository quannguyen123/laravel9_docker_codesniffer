<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class JobLocation.
 *
 * @package namespace App\Models;
 */
class JobLocation extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'job_location';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'status',
        'company_id',
        'province_id',
    ];

}
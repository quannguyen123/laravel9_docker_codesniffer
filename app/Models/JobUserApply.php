<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CompanyLocation.
 *
 * @package namespace App\Models;
 */
class JobUserApply extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'job_user_apply';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'job_id',
        'position',
        'number_phone',
        'file_cv',
    ];

}

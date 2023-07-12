<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Occupation.
 *
 * @package namespace App\Models;
 */
class Occupation extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;
    
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

}

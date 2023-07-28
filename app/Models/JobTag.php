<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TagRepository.
 *
 * @package namespace App\Models;
 */
class JobTag extends Model implements Transformable
{
    use TransformableTrait;
    
    protected $table = 'job_tag';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_id',
        'job_id',
    ];

}

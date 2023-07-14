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
class TagSuggest extends Model implements Transformable
{
    use TransformableTrait;
    
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_id',
        'occupation_id',
        'job_title_id'
    ];

}

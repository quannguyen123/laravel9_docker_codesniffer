<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Company extends Model implements Transformable
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
        'id',
        'name',
        'number_phone',
        'address',
        'size',
        'recipients_of_cv',
        'info',
        'logo',
        'banner',
        'video',
        'purpose',
        'sum_budget_recruitment',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}


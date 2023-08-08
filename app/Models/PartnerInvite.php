<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PartnerInvite.
 *
 * @package namespace App\Models;
 */
class PartnerInvite extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'partner_invite';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'email',
        'first_name',
        'last_name',
        'role',
        'token',
        'expiration_date',
        'company_id',
        'created_by',
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Job.
 *
 * @package namespace App\Models;
 */
class Job extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'job_title',
        'slug',
        'rank',
        'job_type',
        'description',
        'job_require',
        'salary_min',
        'salary_max',
        'show_salary',
        'introducing_letter',
        'language_cv',
        'recipients_of_cv',
        'show_recipients_of_cv',
        'email_recipients_of_cv',
        'post_anonymously',
        'status',
        'company_id',
        'expiration_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function tags() {
        return $this->belongsToMany('App\Models\Tag', 'job_tag', 'job_id', 'tag_id')->where('status', config('custom.status.active'))->select('tags.id', 'tags.name');
    }

    public function occupations() {
        return $this->belongsToMany('App\Models\Occupation', 'job_occupation', 'job_id', 'occupation_id')->where('status', config('custom.status.active'))->select('occupations.id', 'occupations.name', 'occupations.slug');
    }

    public function companyLocation() {
        return $this->belongsToMany('App\Models\CompanyLocation', 'job_location', 'job_id', 'company_location_id')->with('province')->where('status', config('custom.status.active'))->select('company_location.id', 'company_location.name', 'company_location.address', 'company_location.province_id', 'company_location.company_id');
    }

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id')->select('id', 'name', 'number_phone', 'address', 'size', 'recipients_of_cv', 'info', 'logo', 'banner', 'video');
    }

    public function welfare() {
        return $this->belongsToMany('App\Models\Welfare', 'job_welfare', 'job_id', 'welfare_id')->where('status', config('custom.status.active'))->select('welfares.id', 'welfares.name')->withPivot('content');
    }

}

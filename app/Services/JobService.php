<?php

namespace App\Services;

use App\Jobs\SendMailApplyJobForPartner;
use App\Jobs\SendMailApplyJobForUser;
use App\Models\Job;
use App\Models\JobLocation;
use App\Models\JobOccupation;
use App\Models\JobTag;
use App\Models\JobUserApply;
use App\Models\JobUserFavourite;
use App\Models\JobUserView;
use App\Repositories\JobLocationRepository;
use App\Repositories\JobRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class JobService {
    /**
     * function contructer
     *
     * @param JobLocationRepository $repository
     */
    public function __construct(
        protected readonly JobRepository $repository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($request, $status)
    {   
        $requestData = $request->only([
            'status',
            'export',
            'filters'
        ]);

        return $this->repository->getJobByStatus($requestData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestData = $request->only([
            'job_title',
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
            'tag_ids',
            'occupation_ids',
            'company_location_ids',
            'is_draft'
        ]);

        $jobStatus = config('custom.job-status.draft');
        $expirationDate = null;
        if (!empty($requestData['is_draft']) && $requestData['is_draft'] == 1) {
            // return $requestData['is_draft'];
            $jobStatus = config('custom.job-status.draft');
        } else {

            $companyService = DB::table('company_service')
                                ->select('company_service.id', 'company_service.company_id', 'company_service.expiration_date')
                                ->leftJoin('services', 'services.id', '=', 'company_service.service_id')
                                ->where('services.type', config('custom.service-type.post-job'))
                                ->where('company_service.expiration_date', '>=', Carbon::today())
                                ->where('company_service.company_id', Auth::guard('api-user')->user()->company[0]['id'])
                                ->orderBy('company_service.expiration_date', 'DESC')
                                ->first();
 
            if (!empty($companyService)) {
                $jobStatus = config('custom.job-status.public');
                $expirationDate = $companyService->expiration_date;
            }
        }
        
        $job = [
            'job_title' => !empty($requestData['job_title']) ? $requestData['job_title'] : null,
            'slug' => empty($requestData['slug']) ? createSlug($requestData['job_title']) : createSlug($requestData['slug']),
            'rank' => !empty($requestData['rank']) ? $requestData['rank'] : null,
            'job_type' => !empty($requestData['job_type']) ? $requestData['job_type'] : null,
            'description' => !empty($requestData['description']) ? $requestData['description'] : null,
            'job_require' => !empty($requestData['job_require']) ? $requestData['job_require'] : null,
            'salary_min' => !empty($requestData['salary_min']) ? $requestData['salary_min'] : null,
            'salary_max' => !empty($requestData['salary_max']) ? $requestData['salary_max'] : null,
            'show_salary' => !empty($requestData['show_salary']) ? $requestData['show_salary'] : null,
            'introducing_letter' => !empty($requestData['introducing_letter']) && $requestData['introducing_letter'] ? 1 : 0,
            'language_cv' => !empty($requestData['language_cv']) ? $requestData['language_cv'] : null,
            'recipients_of_cv' => !empty($requestData['recipients_of_cv']) ? $requestData['recipients_of_cv'] : null,
            'show_recipients_of_cv' => !empty($requestData['show_recipients_of_cv']) && $requestData['show_recipients_of_cv'] ? 1 : 0,
            'email_recipients_of_cv' => !empty($requestData['email_recipients_of_cv']) ? $requestData['email_recipients_of_cv'] : null,
            'post_anonymously' => !empty($requestData['post_anonymously']) && $requestData['post_anonymously'] ? 1 : 0,
            'company_id' => Auth::guard('api-user')->user()->company[0]['id'],
            'expiration_date' => $expirationDate,
            'created_by' => Auth::guard('api-user')->user()->id
        ];

        $job['status'] = $jobStatus;
        $res['job'] = $this->repository->create($job);

        // occupation_job
        $jobOccupation = [];
        foreach ((array)$requestData['occupation_ids'] as $occupationId) {
            $jobOccupation[] = [
                'occupation_id' => $occupationId,
                'job_id' => $res['job']->id,
            ];
        }
        JobOccupation::insert($jobOccupation);
        
        // job_tag
        $jobTags = [];
        foreach ((array)$requestData['tag_ids'] as $tagId) {
            $jobTags[] = [
                'tag_id' => $tagId,
                'job_id' => $res['job']->id,
            ];
        }
        JobTag::insert($jobTags);

        // job_location
        $jobLocations = [];
        foreach ((array)$requestData['company_location_ids'] as $locationId) {
            $jobLocations[] = [
                'company_location_id' => $locationId,
                'job_id' => $res['job']->id,
            ];
        }
        JobLocation::insert($jobLocations);

        return $res;
    }

    public function detail($id) {
        $job = Job::where('id', $id)
                            ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
                            ->with('tags', 'occupations', 'companyLocation')
                            ->first();

        if (empty($job)) {
            return [false, [], 'Không tìm thấy công việc'];
        }

        return [true, $job, 'Không tìm thấy công việc'];
    }

    public function update(Request $request, $id)
    {
        $job = Job::where('id', $id)
                    ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
                    ->with('tags', 'occupations', 'companyLocation')
                    ->first();

        if (empty($job)) {
            return [false, [], 'Không tìm thấy công việc'];
        }

        $requestData = $request->only([
            'job_title',
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
            'tag_ids',
            'occupation_ids',
            'company_location_ids',
            'is_draft'
        ]);

        $jobStatus = config('custom.job-status.draft');
        $expirationDate = null;
        if (!empty($requestData['is_draft']) && $requestData['is_draft'] == 1) {
            $jobStatus = config('custom.job-status.draft');
        } else {
            $companyService = DB::table('company_service')
                                ->select('company_service.id', 'company_service.company_id', 'company_service.expiration_date')
                                ->leftJoin('services', 'services.id', '=', 'company_service.service_id')
                                ->where('services.type', config('custom.service-type.post-job'))
                                ->where('company_service.expiration_date', '>=', Carbon::today())
                                ->where('company_service.company_id', Auth::guard('api-user')->user()->company[0]['id'])
                                ->orderBy('company_service.expiration_date', 'DESC')
                                ->first();
 
            if (!empty($companyService)) {
                $jobStatus = config('custom.job-status.public');
                $expirationDate = $companyService->expiration_date;
            }
        }

        $job['job_title'] = !empty($requestData['job_title']) ? $requestData['job_title'] : null;
        $job['slug'] = empty($requestData['slug']) ? createSlug($requestData['job_title']) : createSlug($requestData['slug']);
        $job['rank'] = !empty($requestData['rank']) ? $requestData['rank'] : null;
        $job['job_type'] = !empty($requestData['job_type']) ? $requestData['job_type'] : null;
        $job['description'] = !empty($requestData['description']) ? $requestData['description'] : null;
        $job['job_require'] = !empty($requestData['job_require']) ? $requestData['job_require'] : null;
        $job['salary_min'] = !empty($requestData['salary_min']) ? $requestData['salary_min'] : null;
        $job['salary_max'] = !empty($requestData['salary_max']) ? $requestData['salary_max'] : null;
        $job['show_salary'] = !empty($requestData['show_salary']) ? $requestData['show_salary'] : null;
        $job['introducing_letter'] = !empty($requestData['introducing_letter']) && $requestData['introducing_letter'] ? 1 : 0;
        $job['language_cv'] = !empty($requestData['language_cv']) ? $requestData['language_cv'] : null;
        $job['recipients_of_cv'] = !empty($requestData['language_cv']) ? $requestData['recipients_of_cv'] : null;
        $job['show_recipients_of_cv'] = !empty($requestData['show_recipients_of_cv']) && $requestData['show_recipients_of_cv'] ? 1 : 0;
        $job['email_recipients_of_cv'] = !empty($requestData['language_cv']) ? $requestData['email_recipients_of_cv'] : null;
        $job['post_anonymously'] = !empty($requestData['post_anonymously']) && $requestData['post_anonymously'] ? 1 : 0;
        $job['company_id'] = Auth::guard('api-user')->user()->company[0]['id'];
        $job['expiration_date'] = $expirationDate;
        $job['updated_by'] = Auth::guard('api-user')->user()->id;

        $job['status'] = $jobStatus;
        $job->save();
        $res['job'] = $job;

        // occupation_job
        JobOccupation::where('job_id', $res['job']->id)->delete();
        $jobOccupation = [];
        foreach ((array)$requestData['occupation_ids'] as $occupationId) {
            $jobOccupation[] = [
                'occupation_id' => $occupationId,
                'job_id' => $res['job']->id,
            ];
        }
        JobOccupation::insert($jobOccupation);
        
        // job_tag
        JobTag::where('job_id', $res['job']->id)->delete();
        $jobTags = [];
        foreach ((array)$requestData['tag_ids'] as $tagId) {
            $jobTags[] = [
                'tag_id' => $tagId,
                'job_id' => $res['job']->id,
            ];
        }
        JobTag::insert($jobTags);

        // job_location
        JobLocation::where('job_id', $res['job']->id)->delete();
        $jobLocations = [];
        foreach ((array)$requestData['company_location_ids'] as $locationId) {
            $jobLocations[] = [
                'company_location_id' => $locationId,
                'job_id' => $res['job']->id,
            ];
        }
        JobLocation::insert($jobLocations);

        return [true, $res, 'Success'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $job = Job::where('id', $id)
                    ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
                    ->first();

        if (empty($job)) {
            return [false, [], 'Không tìm thấy công việc'];
        }

        $job['deleted_by'] = Auth::guard('api-user')->user()->id;
        $job['deleted_at'] = date("Y-m-d H:i:s", time());
        $job->save();

        return [true, [], 'Xóa bài viết thành công'];
    }

    public function changeStatus($id, $status) {
        $job = Job::where('id', $id)
            ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
            ->first();

        if (empty($job)) {
            return [false, [], 'Không tìm thấy công việc'];
        }

        $status = config('custom.job-status.' . $status);

        if ($status == 'public') {
            $companyService = DB::table('company_service')
                                ->select('company_service.id', 'company_service.company_id')
                                ->leftJoin('services', 'services.id', '=', 'company_service.service_id')
                                ->where('services.type', config('custom.service-type.post-job'))
                                ->where('company_service.expiration_date', '>=', Carbon::today())
                                ->where('company_service.company_id', Auth::guard('api-user')->user()->company[0]['id'])
                                ->get();

            if (empty($companyService)) {
                return [false, [], 'Thay đổi trạng thái không thành công'];
            }
        }

        $job['status'] = $status;
        $job['updated_by'] = Auth::guard('api-user')->user()->id;
        $job->save();

        return [true, $job, 'Thay đổi trạng thái thành công'];
    }

    public function searchJob($request) {
        $requestData = $request->only([
            'occupation_ids',
            'location_id',
            'welfare_ids',
            'search',
            'rank',
            'job_type',
            'salary_type',
            'urgent',
        ]);

        $query = Job::leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
                    ->leftJoin('job_location', 'job_location.job_id', '=', 'jobs.id')
                    ->leftJoin('company_location', 'company_location.id', '=', 'job_location.company_location_id')
                    ->leftJoin('provinces', 'provinces.id', '=', 'company_location.province_id');
                    
        if (!empty($requestData['occupation_ids'])) {
            $query->leftJoin('job_occupation', 'job_occupation.job_id', '=', 'jobs.id')
                ->whereIn('job_occupation.occupation_id', $requestData['occupation_ids']);
        }

        if (!empty($requestData['location_id'])) {
            $query->where('company_location.province_id', $requestData['location_id']);
        }

        if (!empty($requestData['welfare_ids'])) {
            $query->leftJoin('job_welfare', 'job_welfare.job_id', '=', 'jobs.id')
                ->whereIn('job_welfare.welfare_id', $requestData['welfare_ids']);
        }

        if (!empty($requestData['rank'])) {
            $query->where('jobs.rank', $requestData['rank']);
        }

        if (!empty($requestData['job_type'])) {
            $query->where('jobs.job_type', $requestData['job_type']);
        }

        if (!empty($requestData['salary_type'])) {
            switch ($requestData['salary_type']) {
                case 1:
                    $query->where('jobs.salary_max', '<', 500);
                    
                    break;

                case 2:
                    $query->where(function ($query) {
                        $query->whereBetween('jobs.salary_min', [500, 1000])
                            ->orWhereBetween('jobs.salary_max', [500, 1000]);
                    });

                    break;

                case 3:
                    $query->where(function ($query) {
                        $query->whereBetween('jobs.salary_min', [1000, 1500])
                            ->orWhereBetween('jobs.salary_max', [1000, 1500]);
                    });

                    break;

                case 4:
                    $query->where(function ($query) {
                        $query->whereBetween('jobs.salary_min', [1500, 2000])
                            ->orWhereBetween('jobs.salary_max', [1500, 2000]);
                    });

                    break;

                case 5:
                    $query->where(function ($query) {
                        $query->whereBetween('jobs.salary_min', [2000, 3000])
                            ->orWhereBetween('jobs.salary_max', [2000, 3000]);
                    });
                
                    break;

                case 5:
                    $query->where('jobs.salary_min', '>',3000);
                
                    break;
            }
        }

        if (!empty($requestData['urgent'])) {
            $query->leftJoin('job_urgent', 'job_urgent.job_id', '=', 'jobs.id')
                ->whereIn('job_urgent.expiration_date', '>', date("Y-m-d", time()));
        }

        if (!empty($requestData['search'])) {
            $query->where('jobs.job_title', 'LIKE', '%'.$requestData['search'].'%');
        }

        $query->where('jobs.expiration_date', '>', date("Y-m-d", time()))
                ->where('jobs.status', config('custom.job-status.public'));
        $query->with('tags', 'companyLocation', 'company');
        $query->select(
            'jobs.id',
            'jobs.job_title',
            'jobs.slug',
            'jobs.rank',
            'jobs.job_type',
            'jobs.description',
            'jobs.job_require',
            'jobs.salary_min',
            'jobs.salary_max',
            'jobs.show_salary',
            'jobs.created_at',
            'jobs.company_id',
            'jobs.expiration_date',
            'jobs.status',
            'companies.name as company_name',
            'companies.logo',
            DB::raw('group_concat(provinces.name) as province_name')
        );
        $query->groupBy('jobs.id');
        
        $limit = config('custom.paginate');

        if (!empty(Cookie::get('limit')) && in_array(Cookie::get('limit'), (array)config('custom.page-limit'))) {
            $limit = Cookie::get('limit');
        }

        $jobs = $query->paginate($limit);

        return [true, $jobs, 'Success'];
    }

    public function jobDetail($id) {
        $job = Job::where('id', $id)
                    ->where('jobs.expiration_date', '>', date("Y-m-d", time()))
                    ->where('jobs.status', config('custom.job-status.public'))
                    ->with('tags', 'companyLocation', 'company')
                    ->first();

        if (empty($job)) {
            return [true, [], 'Job không tồn tại'];
        }

        $userJobView = [
            'user_id' => Auth::guard('api-user')->user()->id,
            'job_id' => $id
        ];

        JobUserView::insert($userJobView);

        return [true, $job, 'Success'];
    }

    public function applyJob($request, $id) {
        $requestData = $request->only([
            'position',
            'number_phone',
            'file_cv',
        ]);

        $job = Job::where('id', $id)
                    ->where('jobs.expiration_date', '>', date("Y-m-d", time()))
                    ->where('jobs.status', config('custom.job-status.public'))
                    ->with('tags', 'occupations', 'companyLocation', 'company', 'welfare')
                    ->first();

        $company = $job->company->toArray();

        if (empty($job)) {
            return [true, [], 'Job không tồn tại'];
        }

        $user = Auth::guard('api-user')->user();
        $checkExistApply = JobUserApply::where('user_id', $user->id)
                                        ->where('job_id', $id)->first();
        
        if (!empty($checkExistApply)) {
            return [true, $job, 'Job đã apply trước đó'];
        }

        $userJobApply = [
            'user_id' => $user->id,
            'job_id' => $id,
            'position' => $requestData['position'],
            'number_phone' => $requestData['number_phone'],
        ];

        if (!empty($request->file('file_cv'))) {
            $file = $request->file('file_cv');
            $file_name = time().'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('/images/cv');
            $file->move($destinationPath, $file_name);
            $userJobApply['file_cv'] = $file_name;
        } else {
            $userJobApply['file_cv'] = $user->file_cv;
        }
        JobUserApply::insert($userJobApply);
        $mailData = [
            'user' => $user,
            'company' => $company,
            'job' => $job
        ];
        SendMailApplyJobForUser::dispatch($mailData)->delay((now()->addMinute(1)));
        SendMailApplyJobForPartner::dispatch($mailData)->delay((now()->addMinute(1)));

        return [true, $job, 'Success'];
    }

    public function jobFavourite($id) {
        $job = Job::where('id', $id)
                    ->where('jobs.expiration_date', '>', date("Y-m-d", time()))
                    ->where('jobs.status', config('custom.job-status.public'))
                    ->first();
        if (empty($job)) {
            return [true, [], 'Job không tồn tại'];
        }

        $checkExistFavourite = JobUserFavourite::where('user_id', Auth::guard('api-user')->user()->id)
                                        ->where('job_id', $id)->first();

        if (!empty($checkExistFavourite)) {
            return [true, $job, 'Job đã ở trong danh dánh yêu thích'];
        }

        $userJobFavourite = [
            'user_id' => Auth::guard('api-user')->user()->id,
            'job_id' => $id
        ];

        JobUserFavourite::insert($userJobFavourite);
        
        return [true, $job, 'Success'];
    }
}
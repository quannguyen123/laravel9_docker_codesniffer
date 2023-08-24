<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\JobService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class JobController extends BaseController
{
    public function __construct(
        protected JobService $jobService
    ) {
        $this->jobService = $jobService;
    }

    public function ExportExcel($dataExport){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($dataExport);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Customer_ExportedData.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }
    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    function export($jobs){
        $company = Auth::guard('api-user')->user()->company[0];
        $dataExport[] = [$company['name']];
        $dataExport[] = ['Ngày xuất báo cáo:' . Carbon::today()];
        $dataExport[] = ['Tên đăng nhập:' . Auth::guard('api-user')->user()->email];
        $dataExport[] = ['Thư mục: ' . $company['name']];
        $dataExport[] = [];
        $dataExport[] = [];

        $jobIds = $jobs->pluck('id');

        // count user apply job
        $jobUserApply = Job::leftJoin('job_user_apply', 'jobs.id', '=', 'job_user_apply.job_id')
                            ->select('jobs.id', DB::raw('count(*) as total'))
                            ->groupBy('jobs.id')
                            ->whereIn('jobs.id', $jobIds)->get()->toArray();
        $jobUserApplyArr = [];
        foreach($jobUserApply as $item) {
            $jobUserApplyArr[$item['id']] = $item['total'];
        }

        // count user view job
        $jobUserView = Job::leftJoin('job_user_view', 'jobs.id', '=', 'job_user_view.job_id')
                            ->select('jobs.id', DB::raw('count(*) as total'))
                            ->groupBy('jobs.id')
                            ->whereIn('jobs.id', $jobIds)->get()->toArray();
        $jobUserViewArr = [];
        foreach($jobUserView as $item) {
            $jobUserViewArr[$item['id']] = $item['total'];
        }

        $dataExport [] = array("STT", "ID", "Chức Danh", "Lượt xem", "Ngày đăng", "Ngày hết hạn", "Số hồ sơ ứng tuyển");

        $i = 1;
        foreach($jobs as $data_item)
        {
            $i++;
            $dataExport[] = array(
                $i,
                $data_item->id,
                $data_item->job_title,
                $jobUserViewArr[$data_item->id], // lượt xem
                $data_item->created_at,
                $data_item->expiration_date,
                $jobUserApplyArr[$data_item->id] // số hồ sơ ứng tuyển
            );
        }

        $this->ExportExcel($dataExport);
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/partner/job/index",
     *     summary="Danh sách job",
     *     tags={"Partner-Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="status", required=true, description="Id vị trí làm việc", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="filters[occupation]", required=false, description="Id vị trí làm việc", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="filters[location]", required=false, description="Id vị trí làm việc", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            $status = $request->status;
            if (!in_array($status, array_keys(config('custom.job-status')))) {
                return $this->sendError('Status không đúng');
            }
            $jobAll = $this->jobService->index($request, $status);

            $res['jobAll'] = $jobAll;

            if (!empty($request->export) && $request->export == 1) {
                if (empty($jobAll)) {
                    return $this->sendResponse([], 'Không tồn tại job');
                }

                $res = $this->export($jobAll);

                return $this->sendResponse([], 'Success.');
            }

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    /**
     * @OA\Post(
     *     path="/api/partner/job/store",
     *     tags={"Partner-Job"},
     *     summary="Đăng tin tuyển dụng",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"job_title", "rank", "description", "job_require", "salary_min", "salary_max", "recipients_of_cv", "email_recipients_of_cv", "tag_ids[]", "occupation_ids[]", "company_location_ids[]"},
     *                  @OA\Property(property="job_title", type="string ", format="string"),
     *                  @OA\Property(property="rank", type="integer", format="int"),
     *                  @OA\Property(property="job_type", type="integer", format="int"),
     *                  @OA\Property(property="description", type="string", format="string"),
     *                  @OA\Property(property="job_require", type="string", format="string"),
     *                  @OA\Property(property="salary_min", type="integer", format="int"),
     *                  @OA\Property(property="salary_max", type="integer", format="int"),
     *                  @OA\Property(property="show_salary", type="integer", format="int"),
     *                  @OA\Property(property="introducing_letter", type="integer", format="int"),
     *                  @OA\Property(property="language_cv", type="integer", format="int"),
     *                  @OA\Property(property="recipients_of_cv", type="string ", format="string"),
     *                  @OA\Property(property="show_recipients_of_cv", type="integer", format="int"),
     *                  @OA\Property(property="email_recipients_of_cv", type="string", format="string"),
     *                  @OA\Property(property="post_anonymously", type="integer", format="int"),
     *                  @OA\Property(property="tag_ids[]", type="integer", format="int64"),
     *                  @OA\Property(property="occupation_ids[]", type="integer", format="int64"),
     *                  @OA\Property(property="company_location_ids[]", type="integer", format="int64"),
     *                  @OA\Property(property="is_draft", type="integer", format="int")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'job_title' => 'required',
                'rank' => 'required|numeric',
                'job_type' => 'nullable|numeric',
                'description' => 'required',
                'job_require' => 'required',
                'salary_min' => 'required|numeric',
                'salary_max' => 'required|numeric',
                'show_salary' => 'nullable|boolean',
                'introducing_letter' => 'nullable|boolean',
                'language_cv' => 'nullable|numeric',
                'recipients_of_cv' => 'required',
                'show_recipients_of_cv' => 'nullable|boolean',
                'email_recipients_of_cv' => 'email|required',
                'post_anonymously' => 'nullable|boolean',
                'tag_ids' => 'required|array',
                'tag_ids.*' => 'numeric|exists:tags,id',
                'occupation_ids' => 'required|array',
                'occupation_ids.*' => 'numeric|exists:occupations,id',
                'company_location_ids' => 'required|array',
                'company_location_ids.*' => 'numeric|exists:company_location,id',
                'is_draft' => 'nullable|boolean',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $job = $this->jobService->store($request);
    
            return $this->sendResponse($job, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/partner/job/detail/{id}",
     *     summary="Thông tin chi tiết job",
     *     tags={"Partner-Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id job", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function detail($id)
    {
        try {
            [$status, $res['job'], $mess] = $this->jobService->detail($id);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *     path="/api/partner/job/update/{id}",
     *     tags={"Partner-Job"},
     *     summary="Cập nhật thông tin tuyển dụng",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id job", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"job_title", "rank", "description", "job_require", "salary_min", "salary_max", "recipients_of_cv", "email_recipients_of_cv", "tag_ids[]", "occupation_ids[]", "company_location_ids[]"},
     *                  @OA\Property(property="job_title", type="string ", format="string"),
     *                  @OA\Property(property="rank", type="integer", format="int"),
     *                  @OA\Property(property="job_type", type="integer", format="int"),
     *                  @OA\Property(property="description", type="string", format="string"),
     *                  @OA\Property(property="job_require", type="string", format="string"),
     *                  @OA\Property(property="salary_min", type="integer", format="int"),
     *                  @OA\Property(property="salary_max", type="integer", format="int"),
     *                  @OA\Property(property="show_salary", type="integer", format="int"),
     *                  @OA\Property(property="introducing_letter", type="integer", format="int"),
     *                  @OA\Property(property="language_cv", type="integer", format="int"),
     *                  @OA\Property(property="recipients_of_cv", type="string ", format="string"),
     *                  @OA\Property(property="show_recipients_of_cv", type="integer", format="int"),
     *                  @OA\Property(property="email_recipients_of_cv", type="string", format="string"),
     *                  @OA\Property(property="post_anonymously", type="integer", format="int"),
     *                  @OA\Property(property="tag_ids[]", type="integer", format="int64"),
     *                  @OA\Property(property="occupation_ids[]", type="integer", format="int64"),
     *                  @OA\Property(property="company_location_ids[]", type="integer", format="int64"),
     *                  @OA\Property(property="is_draft", type="integer", format="int")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'job_title' => 'required',
                'rank' => 'required|numeric',
                'job_type' => 'nullable|numeric',
                'description' => 'required',
                'job_require' => 'required',
                'salary_min' => 'required|numeric',
                'salary_max' => 'required|numeric',
                'show_salary' => 'nullable|boolean',
                'introducing_letter' => 'nullable|boolean',
                'language_cv' => 'nullable|numeric',
                'recipients_of_cv' => 'required',
                'show_recipients_of_cv' => 'nullable|boolean',
                'email_recipients_of_cv' => 'email|required',
                'post_anonymously' => 'nullable|boolean',

                'tag_ids' => 'required|array',
                'tag_ids.*' => 'numeric|exists:tags,id',
                'occupation_ids' => 'required|array',
                'occupation_ids.*' => 'numeric|exists:occupations,id',
                'company_location_ids' => 'required|array',
                'company_location_ids.*' => 'numeric|exists:company_location,id',
                'is_draft' => 'nullable|boolean'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $res, $mess] = $this->jobService->update($request, $id);
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/partner/job/destroy/{id}",
     *     summary="Xóa job",
     *     tags={"Partner-Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id job", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function destroy($id)
    {
        try {
            [$status, $data, $mess] = $this->jobService->destroy($id);
            
            if ($status) {
                return $this->sendResponse($data, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/partner/job/change-status/{id}/{status}",
     *     summary="Thay đổi trạng thái của job",
     *     tags={"Partner-Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id job", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="path", name="status", required=true, description="draft, public, hidden, about_to_expire, expired, virtual", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function changeStatus($id, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.job-status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $res['job'], $mess] = $this->jobService->changeStatus($id, $status);
            
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}

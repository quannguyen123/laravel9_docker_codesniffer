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
                'is_draft' => 'nullable|boolean'
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

    public function changeStatus(Request $request, $id) {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $status = $request['status'];
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

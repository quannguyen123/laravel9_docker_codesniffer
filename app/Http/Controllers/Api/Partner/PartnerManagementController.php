<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Services\Admins\PartnerManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartnerManagementController extends BaseController
{
    public function __construct(
        protected PartnerManagementService $partnerManagementService
    ) {
        $this->partnerManagementService = $partnerManagementService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $partner = $this->partnerManagementService->index($request);

            $res['partners'] = $partner;

            return $this->sendResponse($res, 'Partner register successfully.');
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
        //
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
            $partner = $this->partnerManagementService->detail($id);

            $res['partner'] = $partner;

            return $this->sendResponse($res, 'Partner register successfully.');
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
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
    
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);

            DB::beginTransaction();
            $user = User::create($input);
            $user->assignRole('partner');
            DB::commit();

            $success['token'] =  $user->createToken('MyApp-Partner')->accessToken;
            $success['name'] =  $user->name;
    
            return $this->sendResponse($success, 'Partner register successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
        //
    }
}

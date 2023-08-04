<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(
        protected UserService $userService
    ) {
        $this->userService = $userService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $userAll = $this->userService->index($request);

            $res['userAll'] = $userAll;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        try {
            [$status, $user, $mess] = $this->userService->detail($id);

            $res['user'] = $user;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function changeStatus($id, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $data, $mess] = $this->userService->changeStatus($id, $status);
            
            return $this->sendResponse($data, $mess) ;
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
            [$status, $data, $mess] = $this->userService->destroy($id);
            
            return $this->sendResponse($data, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}

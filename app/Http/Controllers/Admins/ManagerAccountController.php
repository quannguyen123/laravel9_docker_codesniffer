<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class ManagerAccountController extends BaseController
{
    public function index()
    {
        // $products = Product::all();
    
        // return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
        return $this->sendResponse('quan nguyen', 'Products retrieved successfully.');

    }
}

<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SendMailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMail()
    {
        $details = [
            'title' => 'Mail from Nguyen van Quan',
            'body' => 'This is for testing email using smtp'
        ];
       
        \Mail::to('xzu85644@nezid.com')->send(new \App\Mail\MyTestMail($details));
       
        dd("Email is Sent.");
    }

   
}

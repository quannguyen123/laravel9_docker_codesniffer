<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Mail\SendMailInvitePartner;
use Illuminate\Http\Request;
use Mail;

class PartnerController extends Controller
{
    public function index() {
        $mailData = [
            'title' => 'Mail from ItSolutionStuff.com',
            'body' => 'This is for testing email using smtp.'
        ];
         
        Mail::to('hoangquan.it.hcm3@gmail.com')->send(new SendMailInvitePartner($mailData));
           
        dd("Email is sent successfully.");
    }
}

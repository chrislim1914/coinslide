<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller {
    
    public function mail(){
        Mail::raw('Raw string email', function($msg) { 
            $msg->to(['lm.chrstphr.m@gmail.com']); 
            $msg->from(['chrislim.uth702@gmail.com']); });
    }
}
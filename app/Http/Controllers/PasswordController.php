<?php

namespace App\Http\Controllers;

use App\ResetPassword;

class PasswordController extends Controller
{
    use ResetPassword;

    public function __construct()
    {
        $this->broker = 'users';
    }
}
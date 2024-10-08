<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller; // Ensure this is included
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        // return view('auth.login'); // Adjust according to your view structure
    }

    public function login(Request $request)
    {
        // Your login logic here
    }

    public function logout(Request $request)
    {
        // Your logout logic here
    }
}

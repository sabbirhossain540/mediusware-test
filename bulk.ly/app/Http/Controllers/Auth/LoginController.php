<?php

namespace Bulkly\Http\Controllers\Auth;

use Bulkly\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
  

     public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function loginNow(Request $request){

        $input = $request->input();
        $validator = Validator::make($input,[
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        try {

            $credentials = $request->only('email', 'password');
            $remember = isset($request->remember) ? true : false;
            if (Auth::attempt($credentials, $remember)) {
                return redirect()->route('home');
            } else {
                return redirect()->back()->withErrors(['email' => ['Invalid credentials']]);
            }

        } catch (\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }

    }

    



    
}

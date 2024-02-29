<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as Validator;

class AdminLoginController extends Controller
{
    public function index(){
        return view('admin.login');
    }

    public function authenticate(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            return redirect()->route('admin.dashboard');

            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => 
            $request->password], $request->get('remember'))){
                return redirect()->route('admin.dashboard');
                




            } else{
                return redirect()->route('admin.login')->with('error', 'Either Email/Password is incorreect');
            }

        } else{
            return redirect()->route('admin.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));

        }
        

    }
}

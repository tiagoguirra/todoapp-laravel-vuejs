<?php

namespace App\Http\Controllers\Auth;

use App\User;
use baseController;
use ModelForApi;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends baseController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function register(Request $request){
        $validate = Validator::make(
            $request->all(), [
                'name' => 'required|string|max:255',                
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]
        );
        if ($validate->fails()) {
            return $this->sendValidationError($validate->errors());
        }

        $user = User::create(
            [
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password)
            ]
        );
        return $this->sendResponse('Created',ModelForApi::getElement($user),201);
    }
}

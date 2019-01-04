<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use baseController;
use Illuminate\Http\Request; 
use \Laravel\Passport\Passport;
use Illuminate\Support\Facades\Hash;

class LoginController extends baseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */   

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    private function credentials($request) {       

        return [
            'email' => $request->email,
            'password' => $request->password,            
        ];

    }
    public function login(Request $request) { 

        $user = User::where('email',$request->email)->first();
        if($user){
            if (Hash::check($request->password, $user->password)) {
                $accessToken = $user->createToken('TodoApp', ['*']);
                $oauthToken = $accessToken->token;
                $loginAccess = collect($accessToken); 
                $expiration = $loginAccess['token']->expires_at;            
                $loginAccess->pull('token'); //remove token instance 
                $loginAccess['token_expiration'] = $expiration;

                return $this->sendResponse('Authorized', $loginAccess, 200);
            }
        }
        return $this->sendError('Unauthorized', ['Invalid username or password'],401);         
    }

    public function logout() {

        $user = Auth::guard('api')->user();
        $token = Passport::token()->where('id', $user->token()->id)->update(['revoked' => true]);
        return $this->sendResponse('Ok', null, 200);
        
    }
}

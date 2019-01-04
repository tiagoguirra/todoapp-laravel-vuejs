<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use ModelForApi;
use Validator;
use baseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends baseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(){
        $user = Auth::user();
        return $this->sendResponse('Ok',ModelForApi::getElement($user),200);
    }
    public function update(Request $request){
        $validate = Validator::make(
            $request->all(), [
                'name' => 'required|string|max:255',                
                'email' => 'required|string|email|max:255|unique:users'                
            ]
        );
        if ($validate->fails()) {
            return $this->sendValidationError($validate->errors());
        }
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        if($user->save()){
            return $this->sendResponse('Updated',ModelForApi::getElement($user),200);            
        }
        return $this->sendError('Bad request','Failed to update data',400);
    }
    public function updatePassword(Request $request){
        $validate = Validator::make(
            $request->all(), [
                'old_password'=>'required|string|min:6',
                'password' => 'required|string|min:6|confirmed',
            ]
        );
        if ($validate->fails()) {
            return $this->sendValidationError($validate->errors());
        }
        $user = Auth::user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            if($user->save()){
                return $this->sendResponse('Updated',ModelForApi::getElement($user),200);            
            }
            return $this->sendError('Bad request','Failed to update data',400);
        }
        return $this->sendError('Bad request','Old password do not match',400);
    }
}

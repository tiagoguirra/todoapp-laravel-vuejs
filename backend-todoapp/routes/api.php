<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('auth')->group(function(){
    Route::post('/login','Auth\LoginController@login');
    Route::get('/logout','Auth\LoginController@logout');
    Route::post('/register','Auth\RegisterController@register');
});

Route::prefix('task')->group(function(){
    Route::get('/','TaskController@index');
    Route::get('/{id}','TaskController@show');
    Route::post('/','TaskController@store');
    Route::put('/{id}','TaskController@update');
    Route::delete('/{id}','TaskController@delete');
});

Route::prefix('user')->group(function(){
    Route::get('/','UserController@show');
    Route::put('/','UserController@update');
    Route::put('/password','UserController@updatePassword');
});
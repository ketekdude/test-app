<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix("web/v1")->group(function(){
    Route::get('clear', 'ChargesController@clearSession');
    Route::post('login', 'Login@login');
    Route::post('get_jemaat', 'Jemaat@get');
    Route::post('get_friends', 'Friends@get');
 });
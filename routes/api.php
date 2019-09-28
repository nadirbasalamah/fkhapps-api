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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    //public route
    Route::post('loginStudent', 'AuthController@loginStudent');
    Route::post('loginLecturer', 'AuthController@loginLecturer');
    Route::post('registerStudent', 'AuthController@registerStudent');
    Route::post('registerLecturer', 'AuthController@registerLecturer');
    //private route
    Route::middleware('auth:api')->group(function () {
        Route::post('logoutStudent', 'AuthController@logoutStudent');
        Route::post('logoutLecturer', 'AuthController@logoutLecturer');
    });
});
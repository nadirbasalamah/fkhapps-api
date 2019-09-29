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
    Route::get('getAllProposal','ProposalController@getAllProposal');
    Route::get('getProposalByStudentId/{id}', 'ProposalController@getProposalByStudentId');
    Route::get('getProposalByTitle/{title}', 'ProposalController@getProposalByTitle');
    
    //private route
    Route::middleware('auth:student')->group(function () {
        Route::get('getAllLecturers', 'StudentController@getAllLecturers');
        Route::post('logoutStudent', 'AuthController@logoutStudent');
    });

    Route::middleware('auth:lecturer')->group(function () {
        Route::get('getAllProposalsByLecturerId','ProposalController@getAllProposalsByLecturerId');
        Route::post('logoutLecturer', 'AuthController@logoutLecturer');
    });
});
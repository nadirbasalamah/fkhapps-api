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
    Route::get('getProposalById/{id}', 'ProposalController@getProposalById');
    Route::get('getProposalByStudentId/{id}', 'ProposalController@getProposalByStudentId');
    Route::get('getProposalByTitle/{title}', 'ProposalController@getProposalByTitle');

    Route::get('getAllReport','ReportController@getAllReport');
    Route::get('getReportById/{id}', 'ReportController@getReportById');
    Route::get('getReportByStudentId/{id}', 'ReportController@getReportByStudentId');
    Route::get('getReportByTitle/{title}', 'ReportController@getReportByTitle');
    
    //private routes for student
    Route::middleware('auth:student')->group(function () {
        Route::get('getAllLecturers', 'StudentController@getAllLecturers');
        Route::post('uploadProposal','StudentController@uploadProposal');
        Route::post('uploadReport','StudentController@uploadReport');
        Route::post('logoutStudent', 'AuthController@logoutStudent');
    });

    //private routes for lecturer
    Route::middleware('auth:lecturer')->group(function () {
        Route::get('getAllProposalsByLecturerId','ProposalController@getAllProposalsByLecturerId');
        Route::get('getAllReportsByLecturerId','ReportController@getAllReportsByLecturerId');
        Route::put('verifyProposal/{id}','LecturerController@verifyProposal');
        Route::put('verifyReport/{id}','LecturerController@verifyReport');
        Route::post('logoutLecturer', 'AuthController@logoutLecturer');
    });
});
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
    Route::post('loginAdmin', 'AuthController@loginAdmin');
    Route::post('registerStudent', 'AuthController@registerStudent');
    Route::post('registerLecturer', 'AuthController@registerLecturer');
    //temp
    Route::post('registerAdmin','AuthController@registerAdmin');
    
    Route::get('getAllProposal','ProposalController@getAllProposal');
    Route::get('getProposalById/{id}', 'ProposalController@getProposalById');
    Route::get('getProposalByStudentId/{id}', 'ProposalController@getProposalByStudentId');
    Route::get('getProposalByTitle/{title}', 'ProposalController@getProposalByTitle');

    Route::get('getAllReport','ReportController@getAllReport');
    Route::get('getReportById/{id}', 'ReportController@getReportById');
    Route::get('getReportByStudentId/{id}', 'ReportController@getReportByStudentId');
    Route::get('getReportByTitle/{title}', 'ReportController@getReportByTitle');

    Route::get('getStudentById/{id}', 'StudentController@getStudentById');
    Route::get('getStudentByToken/{token}','StudentController@getStudentByToken');
    Route::get('getLecturerByToken/{token}','LecturerController@getLecturerByToken');

    Route::get('getUserByToken/{token}','AdminController@getUserByToken');

    //private routes for student
    Route::middleware('auth:student')->group(function () {
        Route::post('uploadProposal','StudentController@uploadProposal');
        Route::post('uploadReport','StudentController@uploadReport');
        Route::post('logoutStudent', 'AuthController@logoutStudent');
    });

    //private routes for lecturer
    Route::middleware('auth:lecturer')->group(function () {
        Route::get('getProposalByLecturerId/{id}','ProposalController@getAllProposalsByLecturerId');
        Route::get('getReportByLecturerId/{id}','ReportController@getAllReportsByLecturerId');
        Route::post('verifyProposal/{id}','LecturerController@verifyProposal');
        Route::post('verifyReport/{id}','LecturerController@verifyReport');
        Route::post('logoutLecturer', 'AuthController@logoutLecturer');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('getAllLecturers','AdminController@getAllLecturers');
        Route::get('getLecturerById/{id}','AdminController@getLecturerById');
        Route::get('getLecturerByName/{name}','AdminController@getLecturerByName');

        Route::get('getAcceptedProposalsByLecturerId/{id}','AdminController@getAcceptedProposalsByLecturerId');
        Route::get('getRejectedProposalsByLecturerId/{id}','AdminController@getRejectedProposalsByLecturerId');
        Route::get('getPendingProposalsByLecturerId/{id}','AdminController@getPendingProposalsByLecturerId');

        Route::get('getAcceptedReportsByLecturerId/{id}','AdminController@getAcceptedReportsByLecturerId');
        Route::get('getRejectedReportsByLecturerId/{id}','AdminController@getRejectedReportsByLecturerId');
        Route::get('getPendingReportsByLecturerId/{id}','AdminController@getPendingReportsByLecturerId');
        Route::post('logoutAdmin', 'AuthController@logoutAdmin');
    });
});
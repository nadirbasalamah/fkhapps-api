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
    
    Route::get('getAllProposal','ProposalController@getAllProposal');
    Route::get('getProposalById/{id}', 'ProposalController@getProposalById');
    Route::get('getProposalByStudentId/{id}', 'ProposalController@getProposalByStudentId');
    Route::get('getProposalByTitle/{title}', 'ProposalController@getProposalByTitle');
    Route::get('getAllProposalsByCourse/{course}', 'ProposalController@getAllProposalsByCourse');
    Route::post('getProposalByCourse/{course}','ProposalController@getProposalByCourse');
    Route::post('getProposalByLecturerCourse/{course}','ProposalController@getProposalByLecturerCourse');
    
    Route::get('getAllReport','ReportController@getAllReport');
    Route::get('getReportById/{id}', 'ReportController@getReportById');
    Route::get('getReportByStudentId/{id}', 'ReportController@getReportByStudentId');
    Route::get('getReportByTitle/{title}', 'ReportController@getReportByTitle');
    Route::get('getReportByResult/{result}', 'ReportController@getReportByResult');
    Route::get('getAllReportsByCourse/{course}', 'ReportController@getAllReportsByCourse');
    Route::post('getReportByCourse/{course}','ReportController@getReportByCourse');
    Route::post('getReportByLecturerCourse/{course}','ReportController@getReportByLecturerCourse');
    
    //Route::get('getStudentById/{id}', 'StudentController@getStudentById');
    Route::get('getStudentByToken/{token}','StudentController@getStudentByToken');
    Route::get('getLecturerByToken/{token}','LecturerController@getLecturerByToken');
    
    Route::get('getProposalByLecturerId/{id}','ProposalController@getAllProposalsByLecturerId');
    Route::get('getReportByLecturerId/{id}','ReportController@getAllReportsByLecturerId');
    
    Route::get('getUserByToken/{token}','AdminController@getUserByToken');
    // Route::get('getAllLecturers','AdminController@getAllLecturers');
    
    //TEMPORARY (route sementara)
    // Route::get('getAllRecap','AdminController@getAllRecap');
    // Route::get('getProposalRecapByYear/{year}','AdminController@getProposalRecapByYear');
    // Route::get('getReportRecapByYear/{year}','AdminController@getReportRecapByYear');
    // Route::get('getProposalRecap','AdminController@getProposalRecap');
    // Route::get('getReportRecap','AdminController@getReportRecap');
    //Route::post('uploadProposal','StudentController@uploadProposal');
    // Route::post('uploadReport','StudentController@uploadReport');
    //Route::post('uploadProposal','StudentController@uploadProposal'); //di comment aja 
    // Route::post('setLecturer/{id}','StudentController@setLecturer');
    // Route::post('registerCourse/{id}','StudentController@registerCourse');
    // Route::post('deleteCourse/{id}','StudentController@deleteCourse');
    // Route::post('updateReport/{id}','StudentController@updateReport');
    
    // Route::post('verifyProposal/{id}','LecturerController@verifyProposal');
    // Route::post('verifyReport/{id}','LecturerController@verifyReport');
    
    
    //Route::post('unlockAccess/{id}','AdminController@unlockAccess');
    //Route::get('getAllStudents','AdminController@getAllStudents');
    
    // Route::post('registerLecturerCourse/{id}','LecturerController@registerCourse');
    // Route::post('deleteLecturerCourse/{id}','LecturerController@deleteCourse');
    
    // Route::get('getStudentById/{id}','AdminController@getStudentById');
    // Route::get('getStudentByName/{name}','AdminController@getStudentByName');
    Route::post('lockAccess/{id}','AdminController@lockAccess');
    //Route::post('unlockAccess/{id}','AdminController@unlockAccess');
    
    Route::get('getLecturerByCourse/{course}','AdminController@getLecturerByCourse');

    //Route::get('getStudentByProposalCourse/{course}','AdminController@getAllStudentsByProposalCourse');
    //Route::get('getStudentByReportCourse/{course}','AdminController@getAllStudentsByReportCourse');
    Route::get('getStudentById/{id}','AdminController@getStudentById');
    
    //private route
    Route::middleware('auth:student')->group(function () {
        Route::post('registerCourse/{id}','StudentController@registerCourse');
        Route::post('deleteCourse/{id}','StudentController@deleteCourse');
        Route::post('uploadProposal','StudentController@uploadProposal');
        Route::post('uploadReport','StudentController@uploadReport');
        Route::post('updateReport/{id}','StudentController@updateReport');
        
        Route::post('logoutStudent', 'AuthController@logoutStudent');
    });

    Route::middleware('auth:lecturer')->group(function () {
        Route::post('verifyProposal/{id}','LecturerController@verifyProposal');
        Route::post('verifyReport/{id}','LecturerController@verifyReport');
        Route::post('registerLecturerCourse/{id}','LecturerController@registerCourse');
        Route::post('deleteLecturerCourse/{id}','LecturerController@deleteCourse');
        Route::post('logoutLecturer', 'AuthController@logoutLecturer');
    });
    
    Route::middleware('auth:admin')->group(function () {
        Route::post('unlockAccess/{id}','AdminController@unlockAccess');
        Route::get('getLecturerById/{id}','AdminController@getLecturerById');
        Route::get('getLecturerByName/{name}','AdminController@getLecturerByName');
        Route::get('getAllLecturers','AdminController@getAllLecturers');
        
        // Route::get('getStudentById/{id}','AdminController@getStudentById');
        Route::get('getStudentByName/{name}','AdminController@getStudentByName');
        Route::get('getStudentByProposalCourse/{course}','AdminController@getAllStudentsByProposalCourse');
        Route::get('getStudentByReportCourse/{course}','AdminController@getAllStudentsByReportCourse');
        
        Route::get('getAllRecap','AdminController@getAllRecap');
        Route::get('getProposalRecapByYear/{year}','AdminController@getProposalRecapByYear');
        Route::get('getReportRecapByYear/{year}','AdminController@getReportRecapByYear');
        Route::get('getProposalRecap','AdminController@getProposalRecap');
        Route::get('getReportRecap','AdminController@getReportRecap');

        Route::get('getAcceptedProposalsByLecturerId/{id}','AdminController@getAcceptedProposalsByLecturerId');
        Route::get('getRejectedProposalsByLecturerId/{id}','AdminController@getRejectedProposalsByLecturerId');
        Route::get('getPendingProposalsByLecturerId/{id}','AdminController@getPendingProposalsByLecturerId');

        Route::get('getAcceptedReportsByLecturerId/{id}','AdminController@getAcceptedReportsByLecturerId');
        Route::get('getRejectedReportsByLecturerId/{id}','AdminController@getRejectedReportsByLecturerId');
        Route::get('getPendingReportsByLecturerId/{id}','AdminController@getPendingReportsByLecturerId');
        Route::post('logoutAdmin', 'AuthController@logoutAdmin');
    });
    
});
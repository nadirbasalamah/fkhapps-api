<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Reports as ReportResource;
use Illuminate\Support\Facades\Auth;
use App\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function getAllReport()
    {
        $criteria = DB::table('reports')
                        ->join('students','reports.id_student','students.id')
                        ->join('lecturers','reports.id_lecturer','lecturers.id')
                        ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
                        ->orderBy('reports.id','DESC')
                        ->get();
        return new ReportResource($criteria);
    }

    public function getAllReportsByLecturerId(Request $request, $id)
    {
        // $lecturer = Auth::user();
        $status = "error";
        $message = "";
        $data = [];
    // if($lecturer){
        $reports = DB::table('reports')
        ->join('students','reports.id_student','students.id')
        ->join('lecturers','reports.id_lecturer','lecturers.id')
        ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('reports.id_lecturer','=',$id)
        ->orderBy('reports.id','DESC')
        ->get();
        $status = "success";
        $message = "data of reports";
        $data = $reports;
    // }
    // else {
    //     $message = "Report not found";
    // }
        return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
        ], 200);
    }

    public function getReportById($id)
    {
        $criteria = DB::table('reports')
        ->join('students','reports.id_student','students.id')
        ->join('lecturers','reports.id_lecturer','lecturers.id')
        ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('reports.id','=',$id)
        ->orderBy('reports.id', 'DESC')
        ->get();
        return new ReportResource($criteria);
    }

    public function getReportByStudentId($id)
    {
        $criteria = DB::table('reports')
        ->join('students','reports.id_student','students.id')
        ->join('lecturers','reports.id_lecturer','lecturers.id')
        ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('reports.id_student','=',$id)
        ->orderBy('reports.id', 'DESC')
        ->get();
        return new ReportResource($criteria);
    }

    public function getReportByTitle($title)
    {
        $criteria = DB::table('reports')
        ->join('students','reports.id_student','students.id')
        ->join('lecturers','reports.id_lecturer','lecturers.id')
        ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('reports.title', 'LIKE', "%".$title."%")
        ->orderBy('reports.id', 'DESC')
        ->get();
        return new ReportResource($criteria);
    }
    
    public function getReportByResult($result)
    {
        $criteria = DB::table('reports')
        ->join('students','reports.id_student','students.id')
        ->join('lecturers','reports.id_lecturer','lecturers.id')
        ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('reports.result_case', 'LIKE', "%".$result."%")
        ->orWhere('reports.result_place', 'LIKE', "%".$result."%")
        ->orWhere('reports.result_time', 'LIKE', "%".$result."%")
        ->orderBy('reports.id', 'DESC')
        ->get();
        return new ReportResource($criteria);
    }
    
    public function getAllReportsByCourse(Request $request, $course)
    {
        $criteria = DB::table('reports')
                ->join('students','reports.id_student','students.id')
                ->join('lecturers','reports.id_lecturer','lecturers.id')
                ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
                ->where('reports.course', 'LIKE', "%".$course."%")
                ->orderBy('reports.id', 'DESC')
                ->get();
                return new ReportResource($criteria);
    }
    
    public function getReportByCourse(Request $request, $course)
    {
        $validator = Validator::make($request->all(), [
            'id_student' => 'required'        
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors->get('id_student') as $msg) {
                $message['id_student'] = $msg;
            }            
        } else {
            $criteria = DB::table('reports')
                    ->join('students','reports.id_student','students.id')
                    ->join('lecturers','reports.id_lecturer','lecturers.id')
                    ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
                    ->where('reports.course', 'LIKE', "%".$course."%")
                    ->where('reports.id_student','=',$request->id_student)
                    ->orderBy('reports.id', 'DESC')
                    ->get();
                    return new ReportResource($criteria);
        }
        
    }
    
    public function getReportByLecturerCourse(Request $request, $course)
    {
        $validator = Validator::make($request->all(), [
            'id_lecturer' => 'required'        
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors->get('id_lecturer') as $msg) {
                $message['id_lecturer'] = $msg;
            }            
        } else {
            $criteria = DB::table('reports')
                    ->join('students','reports.id_student','students.id')
                    ->join('lecturers','reports.id_lecturer','lecturers.id')
                    ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
                    ->where('reports.course', 'LIKE', "%".$course."%")
                    ->where('reports.id_lecturer','=',$request->id_lecturer)
                    ->orderBy('reports.id', 'DESC')
                    ->get();
                    return new ReportResource($criteria);
        }
        
    }
}
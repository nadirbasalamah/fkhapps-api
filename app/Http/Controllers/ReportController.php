<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Reports as ReportResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getAllReport()
    {
        $criteria = DB::table('reports')
                    ->join('students','reports.id_student','students.id')
                    ->join('lecturers','reports.id_lecturer','lecturers.id')
                    ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
                    ->get();
        return new ReportResource($criteria);
    }

    public function getAllReportsByLecturerId(Request $request, $id)
    {
        $status = "error";
        $message = "";
        $data = [];
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
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Reports as ReportResource;
use Illuminate\Support\Facades\Auth;
use App\Report;

class ReportController extends Controller
{
    public function getAllReport()
    {
        $criteria = Report::paginate(6);
        return new ReportResource($criteria);
    }

    public function getAllReportsByLecturerId(Request $request)
    {
        $lecturer = Auth::user();
        $status = "error";
        $message = "";
        $data = [];
    if($lecturer){
        $reports = Report::select('*')
        ->where('id_lecturer','=',$lecturer->id)
        ->orderBy('id','DESC')
        ->get();
        $status = "success";
        $message = "data of reports";
        $data = $reports;
    }
    else {
        $message = "Report not found";
    }
        return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
        ], 200);
    }

    public function getReportById($id)
    {
        $criteria = Report::select('*')
        ->where('id','=',$id)
        ->orderBy('id', 'DESC')
        ->get();
        return new ReportResource($criteria);
    }

    public function getReportByStudentId($id)
    {
        $criteria = Report::select('*')
        ->where('id_student','=',$id)
        ->orderBy('id', 'DESC')
        ->get();
        return new ReportResource($criteria);
    }

    public function getReportByTitle($title)
    {
        $criteria = Report::select('*')
        ->where('title', 'LIKE', "%".$title."%")
        ->orderBy('id', 'DESC')
        ->get();
        return new ReportResource($criteria);
    }
}
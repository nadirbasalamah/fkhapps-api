<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Lecturers as LecturerResource;
use App\Http\Resources\Reports as ReportResource;
use App\Http\Resources\Proposals as ProposalResource;
use App\Http\Resources\Students as StudentResource;
use Illuminate\Support\Facades\Auth;
use App\Lecturer;
use App\Report;
use App\Proposal;
use App\Student;
use App\Admin;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{

    public function getUserByToken($token)
    {
        $isStudentFound = true;
        $isLecturerFound = true;
        $isAdminFound = true;
        
        try {
            $student = Student::where('api_token','=',$token)->firstOrfail();
        } catch (\Throwable $th) {
            $isStudentFound = false;
        }

        try {
            $lecturer = Lecturer::where('api_token','=',$token)->firstOrfail();
        } catch (\Throwable $th) {
            $isLecturerFound = false;
        }

        try {
            $admin = Admin::where('api_token','=',$token)->firstOrfail();
        } catch (\Throwable $th) {
            $isAdminFound = false;
        }
        
        if($isStudentFound) {
            return new StudentResource($student);
        } else if($isLecturerFound) {
            return new LecturerResource($lecturer);
        } else if($isAdminFound) {
            return response()->json([
                'status' => 'success',
                'message' => 'admin data',
                'data' => $admin
            ],200);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, data not found"
            ], 404);
        }
    }

    public function getAllLecturers()
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = Lecturer::select('*')->get();
            return new LecturerResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }

    public function getLecturerById($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = Lecturer::select('*')
            ->where('id','=',$id)
            ->orderBy('id', 'DESC')
            ->get();
            return new LecturerResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }

    public function getLecturerByName($name)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = Lecturer::select('*')
            ->where('name','LIKE',"%".$name."%")
            ->orderBy('name', 'DESC')
            ->get();
            return new LecturerResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }

    public function getAcceptedProposalsByLecturerId($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = DB::table('proposals')
            ->join('students','proposals.id_student','students.id')
            ->join('lecturers','proposals.id_lecturer','lecturers.id')
            ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('proposals.id_lecturer','=',$id)
            ->where('proposals.status','=','approved')
            ->orderBy('proposals.id', 'DESC')
            ->get();
            return new ProposalResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }

    public function getRejectedProposalsByLecturerId($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = DB::table('proposals')
            ->join('students','proposals.id_student','students.id')
            ->join('lecturers','proposals.id_lecturer','lecturers.id')
            ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('proposals.id_lecturer','=',$id)
            ->where('proposals.status','=','rejected')
            ->orderBy('proposals.id', 'DESC')
            ->get();
            return new ProposalResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }

    public function getPendingProposalsByLecturerId($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = DB::table('proposals')
            ->join('students','proposals.id_student','students.id')
            ->join('lecturers','proposals.id_lecturer','lecturers.id')
            ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('proposals.id_lecturer','=',$id)
            ->where('proposals.status','=','waiting')
            ->orderBy('proposals.id', 'DESC')
            ->get();
            return new ProposalResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }

    public function getAcceptedReportsByLecturerId($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = DB::table('reports')
            ->join('students','reports.id_student','students.id')
            ->join('lecturers','reports.id_lecturer','lecturers.id')
            ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('reports.id_lecturer','=',$id)
            ->where('reports.status','=','accepted')
            ->orderBy('reports.id', 'DESC')
            ->get();
            return new ReportResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }

    public function getRejectedReportsByLecturerId($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = DB::table('reports')
            ->join('students','reports.id_student','students.id')
            ->join('lecturers','reports.id_lecturer','lecturers.id')
            ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('reports.id_lecturer','=',$id)
            ->where('reports.status','=','rejected')
            ->orderBy('reports.id', 'DESC')
            ->get();
            return new ReportResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }

    public function getPendingReportsByLecturerId($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = DB::table('reports')
            ->join('students','reports.id_student','students.id')
            ->join('lecturers','reports.id_lecturer','lecturers.id')
            ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('reports.id_lecturer','=',$id)
            ->where('reports.status','=','waiting')
            ->orderBy('reports.id', 'DESC')
            ->get();
            return new ReportResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }
}

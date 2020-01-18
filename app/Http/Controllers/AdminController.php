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


class AdminController extends Controller
{

    public function getUserByToken($token)
    {
        $isStudentFound = true;
        $isLecturerFound = true;
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
        
        if($isStudentFound) {
            return new StudentResource($student);
        } else if($isLecturerFound) {
            return new LecturerResource($lecturer);
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
            $criteria = Proposal::select('*')
            ->where('id_lecturer','=',$id)
            ->where('status','=','accepted')
            ->orderBy('id', 'DESC')
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
            $criteria = Proposal::select('*')
            ->where('id_lecturer','=',$id)
            ->where('status','=','rejected')
            ->orderBy('id', 'DESC')
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
            $criteria = Proposal::select('*')
            ->where('id_lecturer','=',$id)
            ->where('status','=','waiting')
            ->orderBy('id', 'DESC')
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
            $criteria = Report::select('*')
            ->where('id_lecturer','=',$id)
            ->where('status','=','accepted')
            ->orderBy('id', 'DESC')
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
            $criteria = Report::select('*')
            ->where('id_lecturer','=',$id)
            ->where('status','=','rejected')
            ->orderBy('id', 'DESC')
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
            $criteria = Report::select('*')
            ->where('id_lecturer','=',$id)
            ->where('status','=','waiting')
            ->orderBy('id', 'DESC')
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

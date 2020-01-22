<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Proposals as ProposalResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProposalController extends Controller
{
    public function getAllProposal()
    {
        $criteria = DB::table('proposals')
                    ->join('students','proposals.id_student','students.id')
                    ->join('lecturers','proposals.id_lecturer','lecturers.id')
                    ->select('proposals.*','students.name','students.major','lecturers.name AS lecturer_name')
                    ->get();
        return new ProposalResource($criteria);
    }

    public function getAllProposalsByLecturerId(Request $request, $id)
    {
        $lecturer = Auth::user();
        $status = "error";
        $message = "";
        $data = [];
    if($lecturer){
        $proposals = DB::table('proposals')
        ->join('students','proposals.id_student','students.id')
        ->join('lecturers','proposals.id_lecturer','lecturers.id')
        ->select('proposals.*','students.name','students.major','lecturers.name AS lecturer_name')
        ->where('proposals.id_lecturer','=',$id)
        ->orderBy('proposals.id','DESC')
        ->get();
        $status = "success";
        $message = "data of proposals";
        $data = $proposals;
    }
    else {
        $message = "Proposal not found";
    }
        return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
        ], 200);
    }

    public function getProposalById($id)
    {
        $criteria = DB::table('proposals')
        ->join('students','proposals.id_student','students.id')
        ->join('lecturers','proposals.id_lecturer','lecturers.id')
        ->select('proposals.*','students.name','students.major','lecturers.name AS lecturer_name')
        ->where('proposals.id','=',$id)
        ->orderBy('proposals.id', 'DESC')
        ->get();
        return new ProposalResource($criteria);
    }

    public function getProposalByStudentId($id)
    {
        $criteria = DB::table('proposals')
        ->join('students','proposals.id_student','students.id')
        ->join('lecturers','proposals.id_lecturer','lecturers.id')
        ->select('proposals.*','students.name','students.major','lecturers.name AS lecturer_name')
        ->where('proposals.id_student','=',$id)
        ->orderBy('proposals.id', 'DESC')
        ->get();
        return new ProposalResource($criteria);
    }

    public function getProposalByTitle($title)
    {
        $criteria = DB::table('proposals')
        ->join('students','proposals.id_student','students.id')
        ->join('lecturers','proposals.id_lecturer','lecturers.id')
        ->select('proposals.*','students.name','students.major','lecturers.name AS lecturer_name')
        ->where('proposals.title', 'LIKE', "%".$title."%")
        ->orderBy('proposals.id', 'DESC')
        ->get();
        return new ProposalResource($criteria);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Proposals as ProposalResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProposalController extends Controller
{
    public function getAllProposal()
    {
        $criteria = DB::table('proposals')
                    ->join('students','proposals.id_student','students.id')
                    ->join('lecturers','proposals.id_lecturer','lecturers.id')
                    ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
                    ->get();
        return new ProposalResource($criteria);
    }

    public function getAllProposalsByLecturerId(Request $request, $id)
    {
        // $lecturer = Auth::user();
        $status = "error";
        $message = "";
        $data = [];
    // if($lecturer){
        $proposals = DB::table('proposals')
        ->join('students','proposals.id_student','students.id')
        ->join('lecturers','proposals.id_lecturer','lecturers.id')
        ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('proposals.id_lecturer','=',$id)
        ->orderBy('proposals.id','DESC')
        ->get();
        $status = "success";
        $message = "data of proposals";
        $data = $proposals;
    // }
    // else {
    //     $message = "Proposal not found";
    // }
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
        ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
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
        ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
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
        ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('proposals.title', 'LIKE', "%".$title."%")
        ->orderBy('proposals.id', 'DESC')
        ->get();
        return new ProposalResource($criteria);
    }
    
    public function getAllProposalsByCourse(Request $request,$course)
    {

        $criteria = DB::table('proposals')
            ->join('students','proposals.id_student','students.id')
            ->join('lecturers','proposals.id_lecturer','lecturers.id')
            ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('proposals.course', 'LIKE', "%".$course."%")
            ->orderBy('proposals.id', 'DESC')
            ->get();
            return new ProposalResource($criteria);
    }
    
    public function getProposalByCourse(Request $request,$course)
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
            $criteria = DB::table('proposals')
                ->join('students','proposals.id_student','students.id')
                ->join('lecturers','proposals.id_lecturer','lecturers.id')
                ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
                ->where('proposals.course', 'LIKE', "%".$course."%")
                ->where('proposals.id_student','=',$request->id_student)
                ->orderBy('proposals.id', 'DESC')
                ->get();
                return new ProposalResource($criteria);
        }
    }
    
    public function getProposalByLecturerCourse(Request $request,$course)
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
            $criteria = DB::table('proposals')
                ->join('students','proposals.id_student','students.id')
                ->join('lecturers','proposals.id_lecturer','lecturers.id')
                ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
                ->where('proposals.course', 'LIKE', "%".$course."%")
                ->where('proposals.id_lecturer','=',$request->id_lecturer)
                ->orderBy('proposals.id', 'DESC')
                ->get();
                return new ProposalResource($criteria);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Lecturers as LecturerResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Lecturer;
use App\Proposal;

class StudentController extends Controller
{
    public function getAllLecturers(Request $request)
    {
        $student = Auth::user();
        if ($student) {
            $criteria = Lecturer::paginate(6);
            return new LecturerResource($criteria);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'access forbidden, please login first',
            ], 403);
        }
    }

    public function uploadProposal(Request $request)
    {
        $student = Auth::user();
        $status = "error";
        $message = "";
        $data = [];
        $code = 403;
    if($student){
        //TODO: upload proposal
        $validator = Validator::make($request->all(), [
            'id_lecturer' => 'required', 
            'title' => 'required',
            'research_background' => 'required',
            'research_question' => 'required',
            'filename' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        } else {
            $proposal = Proposal::create([
                'id_student' => $student->id,
                'id_lecturer' => $request->id_lecturer,
                'title' => $request->title,
                'research_background' => $request->research_background,
                'research_question' => $request->research_question,
                'filename' => $request->filename,
                'status' => 'uploaded',
            ]);
            if($proposal){
                $status = "success";
                $message = "upload success";
                $data = $proposal->toArray();
                $code = 200;
            }
            else{
                $message = 'upload failed';
            }
        }
    }
    else {
        $message = "Error, access not allowed";
    }
        return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
        ], $code);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Lecturers as LecturerResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Lecturer;
use App\Proposal;
use App\Report;

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
            'title' => 'required',
            'research_background' => 'required',
            'research_question' => 'required',
            'filename' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        } else {
            $proposalName = time() . "proposal.pdf";
            $path = $request->file('filename')->move(public_path("/proposals"), $proposalName);
            $proposalUrl = url("/public/proposals/" . $proposalName);
            $id_lecturer = DB::table('lecturers')->select('id')->where('nip','=','P' . $student->nip)->get(['id'])->pluck('id');
            $proposal = Proposal::create([
                'id_student' => $student->id,
                'id_lecturer' => $id_lecturer[0],
                'title' => $request->title,
                'research_background' => $request->research_background,
                'research_question' => $request->research_question,
                'filename' => $proposalUrl,
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

    public function uploadReport(Request $request)
    {
        $student = Auth::user();
        $status = "error";
        $message = "";
        $data = [];
        $code = 403;
    if($student){
        //TODO: upload report
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
            $reportName = time() . "laporan_akhir.pdf";
            $path = $request->file('filename')->move(public_path("/proposals"), $reportName);
            $reportUrl = url("/public/proposals/" . $reportName);
            $report = Report::create([
                'id_student' => $student->id,
                'id_lecturer' => $request->id_lecturer,
                'title' => $request->title,
                'research_background' => $request->research_background,
                'research_question' => $request->research_question,
                'filename' => $reportUrl,
                'status' => 'uploaded',
            ]);
            if($report){
                $status = "success";
                $message = "upload success";
                $data = $report->toArray();
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

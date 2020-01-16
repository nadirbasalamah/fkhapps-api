<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Students as StudentResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Student;
use App\Proposal;
use App\Report;

class StudentController extends Controller
{
    public function getStudentById($id)
    {
        $criteria = Student::select('*')
        ->where('id','=',$id)
        ->orderBy('id', 'DESC')
        ->get();
        return new StudentResource($criteria);
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
                'status' => 'waiting',
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
            'title' => 'required',
            'research_background' => 'required',
            'research_question' => 'required',
            'filename' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors;
        } else {
            $proposal = Proposal::where('id_student','=',$student->id)->where('status','=','accepted')->firstOrfail();
            if($proposal) {
                $reportName = time() . "laporan_akhir.pdf";
                $path = $request->file('filename')->move(public_path("/proposals"), $reportName);
                $reportUrl = url("/public/proposals/" . $reportName);
                $id_lecturer = DB::table('lecturers')->select('id')->where('nip','=','P' . $student->nip)->get(['id'])->pluck('id');
                $report = Report::create([
                    'id_student' => $student->id,
                    'id_lecturer' => $id_lecturer[0],
                    'title' => $request->title,
                    'research_background' => $request->research_background,
                    'research_question' => $request->research_question,
                    'filename' => $reportUrl,
                    'status' => 'waiting',
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
            } else {
                $message = 'upload failed, proposal has to be accepted first';
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

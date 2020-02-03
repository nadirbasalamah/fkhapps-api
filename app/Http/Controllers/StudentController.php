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
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

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

    public function getStudentByToken($token)
    {
        $criteria = Student::select('*')
        ->where('api_token','=',$token)
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
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'research_background' => 'required',
            'research_question' => 'required',
            'filename' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $msg) {
                $message .= $msg;
            }
        } else {
            $proposalName = time() . "proposal.pdf";
            $path = $request->file('filename')->move(public_path("/proposals"), $proposalName);
            $proposalUrl = url("/public/proposals/" . $proposalName);
            $id_lecturer = DB::table('lecturers')->select('id')->where('nip','=','P' . $student->nip)->get(['id'])->pluck('id');
            $lecturer_email = DB::table('lecturers')->select('email')->where('nip','=','P' . $student->nip)->get(['email'])->pluck('email');
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
                $data = [
                    'message' => 'New proposal has been uploaded by : ' . $student->name,
                    'name' => $student->name,
                    'address' => $student->email,
                    'subject' => 'New Proposal Uploaded'
                ];
                //Mail to lecturer's email
                Mail::to($lecturer_email[0])->send(new TestMail($data));
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
        $isProposalFound = true;
    if($student){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'research_background' => 'required',
            'research_question' => 'required',
            'filename' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $msg) {
                $message .= $msg;
            }
        } else {
            try {
                $proposal = Proposal::where('id_student','=',$student->id)->where('status','=','approved')->firstOrfail();
            } catch (\Throwable $th) {
                $isProposalFound = false;
            }
            if($isProposalFound) {
                $reportName = time() . "laporan_akhir.pdf";
                $path = $request->file('filename')->move(public_path("/proposals"), $reportName);
                $reportUrl = url("/public/proposals/" . $reportName);
                $id_lecturer = DB::table('lecturers')->select('id')->where('nip','=','P' . $student->nip)->get(['id'])->pluck('id');
                $lecturer_email = DB::table('lecturers')->select('email')->where('nip','=','P' . $student->nip)->get(['email'])->pluck('email');
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
                    $data = [
                        'message' => 'New report has been uploaded by : ' . $student->name,
                        'name' => $student->name,
                        'address' => $student->email,
                        'subject' => 'New Report Uploaded'
                    ];
                    //Mail to lecturer's email
                    Mail::to($lecturer_email[0])->send(new TestMail($data));


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

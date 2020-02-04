<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Proposal;
use App\Report;
use App\Lecturer;
use App\Http\Resources\Lecturers as LecturerResource;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class LecturerController extends Controller
{

    public function getLecturerByToken($token)
    {
        $criteria = Lecturer::select('*')
        ->where('api_token','=',$token)
        ->orderBy('id', 'DESC')
        ->get();
        return new LecturerResource($criteria);
    }

    public function verifyProposal(Request $request, $id)
    {
        $message = "";
        $data = [];
        $code = 400;
        $status = 'error';
        $lecturer = Auth::user();
        if ($lecturer) {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $msg) {
                    $message .= $msg;
                }
            } else {
                $proposal = Proposal::find($id);
                if (!is_null($proposal)) {
                    $proposal->status = $request->status;
                    $proposal->notes = $request->notes;
                    $proposal->save();
                    $message = 'proposal status updated!';
                    $code = 200;
                    $data = $proposal;

                    $data = [
                        'message' => 'Proposal has been ' . $request->status . '\n Notes : ' . $request->notes,
                        'name' => $lecturer->name,
                        'address' => $lecturer->email,
                        'subject' => 'Proposal ' . $request->status
                    ];
                    //Mail to student's email
                    $student_email = DB::table('students')->select('email')->where('id','=', $proposal->id_student)->get(['email'])->pluck('email');
                    Mail::to($student_email[0])->send(new TestMail($data));
    
                    $data = $proposal;
                    $status = 'success';
                } else {
                    $message = "Error, proposal not found";
                    $code = 404;
                }
            }
        } else {
            $message = "Error, access not allowed";
            $code = 403;
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
            ], $code);
    }

    public function verifyReport(Request $request, $id)
    {
        $message = "";
        $data = [];
        $code = 400;
        $status = 'error';
        $lecturer = Auth::user();
        if ($lecturer) {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $msg) {
                    $message .= $msg;
                }
            } else {
                $report = Report::find($id);
                if (!is_null($report)) {
                    $report->status = $request->status;
                    $report->notes = $request->notes;
                    $report->save();
                    $message = 'report status updated!';
                    $code = 200;
                    $data = $report;
    
                    $data = [
                        'message' => 'Report has been ' . $request->status . '\n Notes : ' . $request->notes,
                        'name' => $lecturer->name,
                        'address' => $lecturer->email,
                        'subject' => 'Report ' . $request->status
                    ];
                    //Mail to student's email
                    $student_email = DB::table('students')->select('email')->where('id','=', $report->id_student)->get(['email'])->pluck('email');
                    Mail::to($student_email[0])->send(new TestMail($data));

                    $data = $report;
                    $status = 'success';
                } else {
                    $message = "Error, report not found";
                    $code = 404;
                }
            }
        } else {
            $message = "Error, access not allowed";
            $code = 403;
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
            ], $code);
    }
}
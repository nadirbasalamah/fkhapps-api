<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Proposal;
use App\Report;
use App\Lecturer;
use App\Student;
use App\Course;
use App\Lecturer_course;
use App\Http\Resources\Lecturers as LecturerResource;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


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
                if(!is_null($proposal)) {
                    $proposal->status = $request->status;
                    $proposal->notes = $request->notes;
                    if($request->status == 'revise'  && $request->file('filename') !== null) {
                        $proposalName = time() . "proposal." . $request->file('filename')->getClientOriginalExtension();
                        $path = $request->file('filename')->move(public_path("/proposals"), $proposalName);
                        $proposalUrl = url("/proposals/" . $proposalName);
                        $proposal->revision = $proposalUrl;
                    }
                    
                    if($request->status == 'revise') {
                        $targetFile = str_replace('https://nadir008basalamah.000webhostapp.com/','',$proposal->filename);
                        unlink(public_path($targetFile));
                        $proposal->filename = '';
                        
                    }
                    
                    $proposal->save();
                    $message = 'proposal status updated!';
                    $code = 200;
                    $data = $proposal;

                     $data = [
                        'message' => 'Proposal has been ' . $request->status,
                        'name' => $lecturer->name, 
                        'address' => $lecturer->email,
                        'subject' => 'Proposal ' . $request->status,
                        'notes' => 'Notes : ' . $request->notes,
                        'file_link' => ''
                    ];
                    //Mail to student's email
                     $student_email = DB::table('students')->select('email')->where('id','=', $proposal->id_student)->get(['email'])->pluck('email');
                     Mail::to($student_email[0])->send(new TestMail($data));
                    
                    $student = Student::find($proposal->id_student);
                    $student->notif = 'verified';
                    $student->save();
                    
                    $status = 'success';
                    $data = $proposal;
                    
                    
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
                if(!is_null($report)) {
                $report->status = $request->status;
                $report->notes = $request->notes;
                
                if($request->status == 'revise' && $request->file('filename') !== null) {
                        $reportName = time() . "laporan_akhir." . $request->file('filename')->getClientOriginalExtension();
                        $path = $request->file('filename')->move(public_path("/proposals"), $reportName);
                        $reportUrl = url("/proposals/" . $reportName);
                        $report->revision = $reportUrl;
                }
                
                if($request->status == 'revise') {
                    $targetFile = str_replace('https://nadir008basalamah.000webhostapp.com/','',$report->filename);
                    unlink(public_path($targetFile));
                    $report->filename = '';
                }
                
                if($request->status == 'approved') {
                    $report->final = "true";
                }
                
                $report->save();
                $courses = Course::select('*')
                    ->where('id_student','=',$report->id_student)
                    ->where('name','=',$report->course)
                    ->orderBy('id','DESC')
                    ->firstOrFail();
                if($request->status == 'revise') {
                    $courses->access = 'revoked';
                } else {
                    $courses->access = 'granted';
                }
                $courses->save();
                $message = 'report status updated!';
                $code = 200;
                $data = $report;
                
                 $data = [
                     'message' => 'Report has been ' . $request->status,
                     'name' => $lecturer->name,
                     'address' => $lecturer->email,
                     'subject' => 'Report ' . $request->status,
                     'notes' => 'Notes : ' . $request->notes,
                     'file_link' => ''
                 ];
                // //Mail to student's email
                 $student_email = DB::table('students')->select('email')->where('id','=', $report->id_student)->get(['email'])->pluck('email');
                 Mail::to($student_email[0])->send(new TestMail($data));
                
                $student = Student::find($report->id_student);
                $student->notif = 'verified';
                $student->save();
                
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
    
    public function checkDuplicateCourse($id,$course) { //util
        $isFound = false;
        $courses = Lecturer_course::select('*')
                    ->where('id_lecturer','=',$id)
                    ->orderBy('id','DESC')
                    ->get();
        for($i = 0; $i < sizeof($courses); $i++) {
            if($courses[$i]->name === $course) {
                $isFound = true;
                break;
            }
        }
        
        return $isFound;
    }
    
    public function registerCourse(Request $request, $id)
    {
        $message = "";
        $data = [];
        $code = 400;
        $status = 'error';
        $lecturer = Auth::user();
         if ($lecturer) {
            $validator = Validator::make($request->all(), [
                'course' => 'required'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $msg) {
                    $message .= $msg;
                }
            } else {
                $lecturer = Lecturer::find($id);
                $isDuplicate = $this->checkDuplicateCourse($id,$request->course);
                
                if(!is_null($lecturer) && !$isDuplicate) {
                    $course = Lecturer_course::create([
                        'id_lecturer' => $id,
                        'name' => $request->course
                    ]);        
                
                if($course) {
                    $message = 'course registered!';
                    $code = 200;
                    $data = $lecturer;
                    $status = 'success';    
                }
                
                } else {
                    $message = "Error, lecturer not found";
                    $code = 404;
                }
                
                if($isDuplicate) {
                    $message = "Error, course already registered!";
                    $code = 400;
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
    
    public function deleteCourse(Request $request, $id) {
        $lecturer = Auth::user();
        $status = "error";
        $message = [];
        $data = [];
        $code = 403;
        $isCourseFound = true;
    if($lecturer){
                try {
                    $data = Lecturer_course::where('id','=',$id)->firstOrFail();    
                } catch(\Throwable $th) {
                    $isCourseFound = false;
                }
                
                if($isCourseFound) {
                    $data->delete();
                    $status = "success";
                    $message = "delete course success";
                    $code = 200;    
                } else {
                    $message['error'] = 'course not found.';
                    $status = "failed";
                    $code = 404;
                }
                    
      }
      else {
         $message['error'] = "Error, access not allowed";
      }
        return response()->json([
        'status' => $status,
        'message' => $message,
        ], $code);
    }
}
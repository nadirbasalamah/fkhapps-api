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
use App\Lecturer;
use App\Course;
use App\Lecturer_course;
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
        $courses = Course::select('courses.id','courses.name')
                    ->where('id_student','=',$id)
                    ->orderBy('id','DESC')
                    ->get();
        // return new StudentResource($criteria);
        return response()->json([
        'status' => 'success',
        'message' => 'student data',
        'data' => $criteria,
        'course' => $courses
        ], 200);
    }
    
    public function getStudentByToken($token)
    {
        $criteria = Student::where('api_token','=',$token)->firstOrFail();
        if(!is_null($criteria)) {
            $courses = Course::select('courses.id','courses.name')
                    ->where('id_student','=',$criteria->id)
                    ->orderBy('id','DESC')
                    ->get();
        
            return response()->json([
            'status' => 'success',
            'message' => 'student data',
            'data' => $criteria,
            'course' => $courses
            ], 200);
        } else {
            return response()->json([
            'status' => 'failed',
            'message' => 'student data not found',
            ], 404);
        }
        return new StudentResource($criteria);
    }
    
    public function deleteCourse(Request $request, $id) {
        $student = Auth::user();
        $status = "error";
        $message = [];
        $data = [];
        $code = 403;
        $isCourseFound = true;
    if($student){
                try {
                    $data = Course::where('id','=',$id)->firstOrFail();    
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
    
    public function getCoursesByLecturerId($id,$course) { //util
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
    
    public function checkDuplicateCourse($id,$course) { //util
        $isFound = false;
        $courses = Course::select('*')
                    ->where('id_student','=',$id)
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
    
    public function registerCourse(Request $request, $id) {
        $student = Auth::user();
        $status = "error";
        $message = [];
        $data = [];
        $code = 403;
        $isStudentFound = true;
        $isLecturerFound = true;
        $isCourseFound = false;
        $lecturerName = "";
        $lecturerCourse = "";
        $lecturerId = 0;
    if($student){
        $validator = Validator::make($request->all(), [
            'course' => 'required',
            'nip' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors->get('course') as $msg) {
                $message['course'] = $msg;
            }
            foreach($errors->get('nip') as $msg) {
                $message['nip'] = $msg;
            }
            
        } else {
            try {
                $student = Student::where('id','=',$id)->firstOrFail();    
            } catch(\Throwable $th) {
                $isStudentFound = false;
            }
            if($isStudentFound) {
                try {
                    $lecturer = Lecturer::where('nip','=','P'.$request->nip)->firstOrFail();
                    $lecturerName = $lecturer->name;
                    $lecturerId = $lecturer->id;
                } catch(\Throwable $th) {
                    $isLecturerFound = false;
                }
                
                if(!$isLecturerFound) {
                    $message['error'] = 'lecturer not found.';
                } else {
                    $isCourseFound = $this->getCoursesByLecturerId($lecturerId,$request->course);
                    
                    if($isCourseFound) {
                        $student->nip = $request->nip;
                        $student->save();
                    
                        $course = Course::create([
                            'id_student' => $id,
                            'name' => $request->course,
                            'nip' => $request->nip,
                            'lecturer_name' => $lecturerName
                        ]);    
                    
                    } else {
                        $message['error'] = 'course not found.';
                    }
                    
                }
                if($course && $isLecturerFound) {
                    $status = "success";
                    $message = "register course success";
                    $data = $student->toArray();
                    $code = 200;    
                }
                
            } else {
                $message['error'] = 'student not found.';
            }
        }
      }
      else {
         $message['error'] = "Error, access not allowed";
      }
        return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
        ], $code);
    }

    public function uploadProposal(Request $request)
    {
        $student = Auth::user();
        $status = "error";
        $message = [];
        $data = [];
        $code = 403;
    if($student){
        $validator = Validator::make($request->all(), [
            'course' => 'required',
            'title' => 'required',
            'research_background' => 'required',
            'research_question' => 'required',
            'filename' => 'required|mimes:docx,doc,pdf',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors->get('title') as $msg) {
                $message['title'] = $msg;
            }
            
            foreach($errors->get('research_background') as $msg) {
                $message['research_background'] = $msg;
            }
            
            foreach($errors->get('research_question') as $msg) {
                $message['research_question'] = $msg;
            }
            
            foreach($errors->get('filename') as $msg) {
                $message['filename'] = $msg;
            }
            foreach($errors->get('course') as $msg) {
                $message['course'] = $msg;
            }
            
        } else {
            $proposalName = time() . "proposal." . $request->file('filename')->getClientOriginalExtension();
            $path = $request->file('filename')->move(public_path("/proposals"), $proposalName);
            $proposalUrl = url("/proposals/" . $proposalName);
            $id_lecturer = DB::table('lecturers')->select('id')->where('nip','=','P' . $student->nip)->get(['id'])->pluck('id');
            $proposal = Proposal::create([
                'id_student' => $student->id,
                // 'id_student' => 1,
                 'id_lecturer' => $id_lecturer[0],
                // 'id_lecturer' => 1,
                 'course' => $request->course,
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
                     'subject' => 'New Proposal Uploaded',
                     'notes' => '',
                     'file_link' => 'Proposal file link ' . $proposalUrl
                 ];
                //Mail to lecturer's email
                 $lecturer_email = DB::table('lecturers')->select('email')->where('nip','=','P' . $student->nip)->get(['email'])->pluck('email');
                 Mail::to($lecturer_email[0])->send(new TestMail($data));  
                
                $status = "success";
                $message = "upload success";
                $data = $proposal->toArray();
                $code = 200;
            }
            else{
                $message['error'] = 'upload failed';
            }
        }
      }
      else {
         $message['error'] = "Error, access not allowed";
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
        $message = [];
        $data = [];
        $code = 403;
        $isProposalFound = true;
    if($student){
        $validator = Validator::make($request->all(), [
            'course' => 'required',
            'title' => 'required',
            'research_background' => 'required',
            'research_question' => 'required',
            'filename' => 'required|mimes:docx,doc,pdf',
            'result_case' => 'required',
            'result_place' => 'required',
            'result_time' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors->get('title') as $msg) {
                $message['title'] = $msg;
            }
            
            foreach($errors->get('research_background') as $msg) {
                $message['research_background'] = $msg;
            }
            
            foreach($errors->get('research_question') as $msg) {
                $message['research_question'] = $msg;
            }
            
            foreach($errors->get('filename') as $msg) {
                $message['filename'] = $msg;
            }
            
            foreach($errors->get('result_case') as $msg) {
                $message['result_case'] = $msg;
            }
            
            foreach($errors->get('result_place') as $msg) {
                $message['result_place'] = $msg;
            }
            
            foreach($errors->get('result_time') as $msg) {
                $message['result_time'] = $msg;
            }
            foreach($errors->get('course') as $msg) {
                $message['course'] = $msg;
            }
        } else {
             try {
                 $proposal = Proposal::where('id_student','=',$student->id)->where('status','=','approved')->firstOrfail();
             } catch (\Throwable $th) {
                 $isProposalFound = false;
             }
            if($isProposalFound) {  
            $reportName = time() . "laporan_akhir." . $request->file('filename')->getClientOriginalExtension();
            $path = $request->file('filename')->move(public_path("/proposals"), $reportName);
            $reportUrl = url("/proposals/" . $reportName);
            $id_lecturer = DB::table('lecturers')->select('id')->where('nip','=','P' . $student->nip)->get(['id'])->pluck('id');
            $report = Report::create([
                'id_student' => $student->id, 
                'id_lecturer' => $id_lecturer[0],
                'course' => $request->course,
                'title' => $request->title,
                'research_background' => $request->research_background,
                'research_question' => $request->research_question,
                'filename' => $reportUrl,
                'result_case' => $request->result_case,
                'result_place' => $request->result_place,
                'result_time' => $request->result_time,
                'status' => 'waiting',
                'final' => 'false'
            ]);    
            
            if($report){
                 $data = [
                         'message' => 'New report has been uploaded by : ' . $student->name, 
                         'name' => $student->name,  
                         'address' => $student->email, 
                         'subject' => 'New Report Uploaded',
                         'notes' => '',
                         'file_link' => 'Report file link ' . $reportUrl
                     ];
                // //Mail to lecturer's email
                 $lecturer_email = DB::table('lecturers')->select('email')->where('nip','=','P' . $student->nip)->get(['email'])->pluck('email');
                Mail::to($lecturer_email[0])->send(new TestMail($data)); 
                
                $status = "success";
                $message = "upload success";
                $data = $report->toArray();
                $code = 200;
            }
            else{
                $message['error'] = 'upload failed';
            }    
            } else {
                $message['error'] = 'upload failed, proposal has to be accepted first';
            }
            
        }
    }
    else {
        $message['error'] = "Error, access not allowed";
    }
        return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
        ], $code);
    }
    
    public function updateReport(Request $request, $id)
    {
        $student = Auth::user();
        $status = "error";
        $message = [];
        $data = [];
        $code = 403;
        $isProposalFound = true;
    if($student){
        $validator = Validator::make($request->all(), [
            'filename'=> 'required|mimes:pdf']);
        if ($validator->fails()) {
            $errors = $validator->errors();
            
            foreach($errors->get('filename') as $msg) {
                $message['filename'] = $msg;
            }
            
        } else {
             try {
                 $proposal = Proposal::where('id_student','=',$id)->where('status','=','approved')->firstOrfail();
             } catch (\Throwable $th) {
                 $isProposalFound = false;
             }
            if($isProposalFound) {  
            $reportName = time() . "laporan_akhir." . $request->file('filename')->getClientOriginalExtension();
            $path = $request->file('filename')->move(public_path("/proposals"), $reportName);
            $reportUrl = url("/proposals/" . $reportName);
            
            $report = Report::where('id_student','=',$id)->where('final','=','true')->firstOrFail();
        
            if(!is_null($report)){
                $report->finalfile = $reportUrl;
                $report->save();
                
                 $data = [
                         'message' => 'New report has been uploaded by : ' . $student->name, 
                         'name' => $student->name,  
                         'address' => $student->email, 
                         'subject' => 'New Report Uploaded',
                         'notes' => '',
                         'file_link' => 'Report file link ' . $reportUrl
                     ];
                //Mail to lecturer's email
                 $lecturer_email = DB::table('lecturers')->select('email')->where('nip','=','P' . $student->nip)->get(['email'])->pluck('email');
                Mail::to($lecturer_email[0])->send(new TestMail($data)); 
                
                $status = "success";
                $message = "upload success";
                $data = $report->toArray();
                $code = 200;
            }
            else{
                $message['error'] = 'upload failed';
            }    
            } else {
                $message['error'] = 'upload failed, proposal has to be accepted first';
            }
            
        }
    }
    else {
        $message['error'] = "Error, access not allowed";
    }
        return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
        ], $code);
    }
}

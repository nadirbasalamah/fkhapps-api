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
use App\Admin;
use App\Course;
use App\Lecturer_course;
use Illuminate\Support\Facades\DB;
use \stdClass;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function getUserByToken($token)
    {
        $isStudentFound = true;
        $isLecturerFound = true;
        $isAdminFound = true;
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
        
        try {
            $admin = Admin::where('api_token','=',$token)->firstOrfail();
        } catch (\Throwable $th) {
            $isAdminFound = false;
        }
        
        if($isStudentFound) {
            $courses = Course::select('*')
                    ->where('id_student','=',$student->id)
                    ->orderBy('id','DESC')
                    ->get();
            return response()->json([
                'status' => 'success',
                'message' => 'student data',
                'data' => $student,
                'course' => $courses
                ], 200);
        } else if($isLecturerFound) {
            $courses = Lecturer_course::select('*')
                    ->where('id_lecturer','=',$lecturer->id)
                    ->orderBy('id','DESC')
                    ->get();
            return response()->json([
                'status' => 'success',
                'message' => 'lecturer data',
                'data' => $lecturer,
                'course' => $courses
                ], 200);
        } else if($isAdminFound) {
            return response()->json([
            'status' => 'success',
            'message' => 'admin data',
            'data' => $admin
            ],200);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, data not found"
            ], 404);
        }
    }
    
    public function lockAccess(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'course' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors->get('course') as $msg) {
                $message['course'] = $msg;
            }            
        } else {
            $course = Course::where('id_student','=',$id)->where('name','=',$request->course)->firstOrFail();
            if(!is_null($course)) {
                $course->access = 'revoked';
                $course->save();
                
                return response()->json([
                    'status' => "success",
                    'message' => "Access for student locked!"
                ], 200);
            } else {
                return response()->json([
                    'status' => "error",
                    'message' => "Error, data not found"
                ], 404);
            }    
        }
        
    }
    
    public function unlockAccess(Request $request, $id) {
        $admin = Auth::user();
        if($admin) {
            $validator = Validator::make($request->all(), [
            'course' => 'required',
            'type' => 'required'
        ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach($errors->get('course') as $msg) {
                    $message['course'] = $msg;
                }
            } else {
                $course = Course::where('id_student','=',$id)->where('name','=',$request->course)->firstOrFail();
                if(!is_null($course)) {
                    $course->access = 'granted';
                    $course->save();
                    
                    $proposal = Proposal::where('id_student','=',$id)->where('course','=',$request->course)->get();
                    $proposalSize = count($proposal);
                    
                    $report = Report::where('id_student','=',$id)->where('course','=',$request->course)->get();
                    $reportSize = count($report);
                    
                    if($request->type === 'proposal') {
                        if($proposalSize !== 0) {
                            $proposal[$proposalSize - 1]->updated_at = Carbon::now()->toDateTimeString();
                            $proposal[$proposalSize - 1]->save();
                        }    
                    }
                    
                    if($request->type === 'laporan') {
                        if($reportSize !== 0) {
                            $report[$reportSize - 1]->updated_at = Carbon::now()->toDateTimeString();
                            $report[$reportSize - 1]->save();
                        }    
                    }
                    
                    
                    return response()->json([
                        'status' => "success",
                        'message' => "Access for student unlocked!"
                    ], 200);
                } else {
                    return response()->json([
                        'status' => "error",
                        'message' => "Error, data not found"
                    ], 404);
                }    
            }  
         } else {
             return response()->json([
                 'status' => "error",
                 'message' => "Error, access forbidden"
            ], 403);
         }
        
    }
    
    public function getAllStudents()
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = Student::select('*')->get();
            // return new StudentResource($criteria);
            $data = [];
            for($i = 0; $i < sizeof($criteria); $i++) {
                $studentData = new stdClass;
                $studentData->data = $criteria[$i];
                $studentData->courses = $this->getCoursesByStudentId($criteria[$i]->id);
                array_push($data,$studentData);
            }
            return response()->json([
                    'status' => "success",
                    'message' => "students data",
                    'data' => $data
                ]);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }
    public function getStudentById($id)
    {
        // $admin = Auth::user();
        // if ($admin) {
            $criteria = Student::select('*')
            ->where('id','=',$id)
            ->orderBy('id', 'DESC')
            ->get();
            $data = [];
            for($i = 0; $i < sizeof($criteria); $i++) {
                $studentData = new stdClass;
                $studentData->data = $criteria[$i];
                $studentData->courses = $this->getCoursesByStudentId($criteria[$i]->id);
                array_push($data,$studentData);
            }
            
            return response()->json([
                    'status' => "success",
                    'message' => "students data",
                    'data' => $data
                ]);
        // } else {
        //     return response()->json([
        //         'status' => "error",
        //         'message' => "Error, access forbidden"
        //     ], 403);
        // }
    }

    public function getStudentByName($name)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = Student::select('*')
            ->where('name','LIKE',"%".$name."%")
            ->orderBy('name', 'DESC')
            ->get();
            $data = [];
            for($i = 0; $i < sizeof($criteria); $i++) {
                $studentData = new stdClass;
                $studentData->data = $criteria[$i];
                $studentData->courses = $this->getCoursesByStudentId($criteria[$i]->id);
                array_push($data,$studentData);
            }
            
            return response()->json([
                    'status' => "success",
                    'message' => "students data",
                    'data' => $data
                ]);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }
    
    public function getCoursesByStudentId($id) { //util
        $courses = Course::select('*')
                    ->where('id_student','=',$id)
                    ->orderBy('id','DESC')
                    ->get();
        
        return $courses;
    }
    
    public function getCoursesByLecturerId($id) { //util
        $courses = Lecturer_course::select('*')
                    ->where('id_lecturer','=',$id)
                    ->orderBy('id','DESC')
                    ->get();
        
        return $courses;
    }
    
    public function getAllStudentsByProposalCourse($course)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = DB::table('students')
            ->join('proposals','students.id','proposals.id_student')
            ->select('students.*')
            ->where('proposals.course', '=', $course)
            ->orderBy('students.id', 'DESC')
            ->distinct()
            ->get();
            
            $approvedCriteria = DB::table('students')
            ->join('proposals','students.id','proposals.id_student')
            ->select('students.*')
            ->where('proposals.course', '=', $course)
            ->where('proposals.status','=','approved')
            ->orWhere('proposals.status','=','waiting')
            ->orderBy('students.id', 'DESC')
            ->distinct()
            ->get();
            
            $approvedData = [];
            for($i = 0; $i < sizeof($approvedCriteria); $i++) {
                array_push($approvedData,$approvedCriteria[$i]->id);    
            }
            
            $data = [];
            for($i = 0; $i < sizeof($criteria); $i++) {
                if(!(in_array($criteria[$i]->id, $approvedData))) {
                    array_push($data,$criteria[$i]);    
                }
            }
            
            return response()->json([
                    'status' => "success",
                    'message' => "students data",
                    'data' => $data
                ]);
        } else {
            return response()->json([
                 'status' => "error",
                 'message' => "Error, access forbidden"
            ], 403);
         }

        
    }
    
    public function getAllStudentsByReportCourse($course)
    {
         $admin = Auth::user();
         if($admin) {
            $criteria = DB::table('students')
            ->join('reports','students.id','reports.id_student')
            ->select('students.*')
            ->where('reports.course', '=', $course)
            ->orderBy('students.id', 'DESC')
            ->distinct()
            ->get();
            
            $approvedCriteria = DB::table('students')
            ->join('reports','students.id','reports.id_student')
            ->select('students.*')
            ->where('reports.course', '=', $course)
            ->where('reports.status','=','approved')
            ->orWhere('reports.status','=','waiting')
            ->orderBy('students.id', 'DESC')
            ->distinct()
            ->get();
            
            $approvedData = [];
            for($i = 0; $i < sizeof($approvedCriteria); $i++) {
                array_push($approvedData,$approvedCriteria[$i]->id);    
            }
            
            $data = [];
            for($i = 0; $i < sizeof($criteria); $i++) {
                if(!(in_array($criteria[$i]->id, $approvedData))) {
                    array_push($data,$criteria[$i]);    
                }
            }
            
            return response()->json([
                    'status' => "success",
                    'message' => "students data",
                    'data' => $data
                ]);
                
         } else {
             return response()->json([
                 'status' => "error",
                 'message' => "Error, access forbidden"
             ], 403);
         }
    }
    
    public function getAllLecturers()
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = Lecturer::select('*')->get();
            // return new LecturerResource($criteria);
            
            $data = [];
            for($i = 0; $i < sizeof($criteria); $i++) {
                $lecturerData = new stdClass;
                $lecturerData->data = $criteria[$i];
                $lecturerData->courses = $this->getCoursesByLecturerId($criteria[$i]->id);
                array_push($data,$lecturerData);
            }
            
            return response()->json([
                    'status' => "success",
                    'message' => "lecturers data",
                    'data' => $data
                ]);
            
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }
    
public function getLecturerByCourse($course)
{
$criteria = DB::table('lecturer_courses')->join('lecturers','lecturer_courses.id_lecturer','lecturers.id')->select('lecturer_courses.*','lecturers.name AS lecturer_name','lecturers.nip')->where('lecturer_courses.name','=',$course)
->get();
return new LecturerResource($criteria);

} 
    
    public function getLecturerById($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = Lecturer::select('*')
            ->where('id','=',$id)
            ->orderBy('id', 'DESC')
            ->get();
            $data = [];
            for($i = 0; $i < sizeof($criteria); $i++) {
                $lecturerData = new stdClass;
                $lecturerData->data = $criteria[$i];
                $lecturerData->courses = $this->getCoursesByLecturerId($criteria[$i]->id);
                array_push($data,$lecturerData);
            }
            
            return response()->json([
                    'status' => "success",
                    'message' => "lecturers data",
                    'data' => $data
                ]);
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
            $data = [];
            for($i = 0; $i < sizeof($criteria); $i++) {
                $lecturerData = new stdClass;
                $lecturerData->data = $criteria[$i];
                $lecturerData->courses = $this->getCoursesByLecturerId($criteria[$i]->id);
                array_push($data,$lecturerData);
            }
            
            return response()->json([
                    'status' => "success",
                    'message' => "lecturers data",
                    'data' => $data
                ]);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }
    
    function getProposalRecapByYear($year)
    {
        $admin = Auth::user();
        if ($admin) {
        $criteria = DB::table('proposals')
        ->join('students','proposals.id_student','students.id')
        ->join('lecturers','proposals.id_lecturer','lecturers.id')
        ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('students.academic_year','=',$year)
        ->get();
        
        return new ProposalResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);    
        } 
    }
    
    function getReportRecapByYear($year)
    {
        $admin = Auth::user();
        if ($admin) {
        $criteria = DB::table('reports')
        ->join('students','reports.id_student','students.id')
        ->join('lecturers','reports.id_lecturer','lecturers.id')
        ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('students.academic_year','=',$year)
        ->where('reports.status','=','approved')
        ->get();
        
        return new ReportResource($criteria);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);    
        }
    }
    
    public function getProposalRecap()
    {
        $admin = Auth::user();
        if ($admin) {
            $years = $this->getAcademicYear();
            $proposals = [];
            $data = [];
            $result = [];
            
            for($i = 0; $i < sizeof($years); $i++) {
                array_push($proposals,$this->getProposalByYear($years[$i]));
            }
            
            for($i = 0; $i < sizeof($years); $i++) {
                $data[$years[$i]] = $proposals[$i];
            }
            
            for($i = 0; $i < sizeof($data); $i++) {
                $object = new stdClass();
                $object->year = $years[$i];
                $object->data = $data[$years[$i]];
                
                array_push($result, $object);
            }

            return response()->json([
                'status' => "success",
                'message' => "Proposal recap data",
                'data' => $result
            ], 200);    
            
            
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }
    
    public function getReportRecap()
    {
        $admin = Auth::user();
        if ($admin) {
            $years = $this->getAcademicYear();
            
            $reports = [];
            $data = [];
            $result = [];
            
            for($i = 0; $i < sizeof($years); $i++) {
                array_push($reports,$this->getReportByYear($years[$i]));
            }
            
            for($i = 0; $i < sizeof($years); $i++) {
                $data[$years[$i]] = $reports[$i];
            }
            
            for($i = 0; $i < sizeof($data); $i++) {
                $object = new stdClass();
                $object->year = $years[$i];
                $object->data = $data[$years[$i]];
                
                array_push($result, $object);
            }
        
            return response()->json([
                'status' => "success",
                'message' => "Report recap data",
                'data' => $result
            ], 200);
            
            
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }
    
    
    public function getAllRecap() {
        $admin = Auth::user();
        if ($admin) {
            
            $criteria = DB::table('students')
                        ->select('*')
                        ->where('nip','!=',null)
                        ->orderBy('id','DESC')->get();
            
            $lecturers = [];
            $students = [];
            $academicYears = [];
            
            $acceptedProposals = [];
            $rejectedProposals = [];
            $acceptedReports = [];
            $rejectedReports = [];
            
            $proposalStatus = [];
            $reportStatus = [];
            
            $data = [];
        
            foreach($criteria as $student) {
                array_push($lecturers,$this->getLecturerByNip($student->nip));
            }
            
            foreach($criteria as $student) {
                array_push($students,$student->name);
            }
            
            foreach($criteria as $student) {
                array_push($academicYears,$student->academic_year);
            }
        
            foreach($criteria as $student) {
                array_push($acceptedProposals,($this->getAllAcceptedProposal($student->id) >= 1 ? 'proposal selesai' : ''));
                array_push($rejectedProposals,($this->getAllRejectedProposal($student->id) >= 1 ? 'revisi ' . $this->getAllRejectedProposal($student->id). ' kali' : ''));
                
                array_push($acceptedReports,($this->getAllAcceptedReport($student->id) >= 1 ? 'laporan akhir selesai' : ''));
                array_push($rejectedReports,($this->getAllRejectedReport($student->id) >= 1 ? 'revisi ' . $this->getAllRejectedReport($student->id). ' kali' : ''));
            }
            
            for($i = 0; $i < sizeof($criteria); $i++) {
              $object = new stdClass();
              $object->student_name = $students[$i];
              $object->lecturer_name = $lecturers[$i];
              $object->academic_year = $academicYears[$i];
              $object->accepted_proposals_status = $acceptedProposals[$i];
              $object->rejected_proposals_status = $rejectedProposals[$i];
              $object->accepted_reports_status = $acceptedReports[$i];
              $object->rejected_reports_status = $rejectedReports[$i];
              
              array_push($data,$object);
            }
            
            return response()->json([
                    'status' => 'success',
                    'message' => 'recap data',
                    'data' => $data
                ]);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Error, access forbidden"
            ], 403);
        }
    }
    //UTIL
    function getAcademicYear() {
        $data = Student::select('academic_year')->distinct()->orderBy('academic_year','ASC')->get();
        
        $result = [];
        
        foreach($data as $dt) {
            array_push($result,$dt->academic_year);
        }
        
        return $result;
    }
    
    function getProposalByYear($year)
    {
        $criteria = DB::table('proposals')
        ->join('students','proposals.id_student','students.id')
        ->join('lecturers','proposals.id_lecturer','lecturers.id')
        ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('students.academic_year','=',$year)
        ->get();
        
        return $criteria;
    }
    
    function getReportByYear($year)
    {
        $criteria = DB::table('reports')
        ->join('students','reports.id_student','students.id')
        ->join('lecturers','reports.id_lecturer','lecturers.id')
        ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
        ->where('students.academic_year','=',$year)
        ->where('reports.status','=','approved')
        ->get();
        
        return $criteria;
    }
    
    
    function getLecturerByNip($nip) {
        $data = Lecturer::select('name')
        ->where('nip','=','P' . $nip)
        ->get();
        
        foreach($data as $dt) {
            $result = $dt->name;
        }
        
        return $result;
    }
    
    function getAllAcceptedProposal($id) {
        $criteria = DB::table('proposals')
            ->select('*')
            ->where('proposals.id_student','=',$id)
            ->where('proposals.status','=','approved')
            ->orderBy('proposals.id_student', 'DESC')
            ->get();
        
        $result = sizeof($criteria);
        return $result;
    }
    
    function getAllRejectedProposal($id) {
        $criteria = DB::table('proposals')
            ->select('*')
            ->where('proposals.id_student','=',$id)
            ->where('proposals.status','=','revise')
            ->orderBy('proposals.id_student', 'DESC')
            ->get();
        
        $result = sizeof($criteria);
        return $result;
    }
    
    function getAllAcceptedReport($id) {
        $criteria = DB::table('reports')
            ->select('*')
            ->where('reports.id_student','=',$id)
            ->where('reports.status','=','approved')
            ->orderBy('reports.id_student', 'DESC')
            ->get();
        
        $result = sizeof($criteria);
        return $result;
    }
    
    function getAllRejectedReport($id) {
        $criteria = DB::table('reports')
            ->select('*')
            ->where('reports.id_student','=',$id)
            ->where('reports.status','=','revise')
            ->orderBy('reports.id_student', 'DESC')
            ->get();
        
        $result = sizeof($criteria);
        return $result;
    }

    public function getAcceptedProposalsByLecturerId($id)
    {
        $admin = Auth::user();
        if ($admin) {
            $criteria = DB::table('proposals')
            ->join('students','proposals.id_student','students.id')
            ->join('lecturers','proposals.id_lecturer','lecturers.id')
            ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('proposals.id_lecturer','=',$id)
            ->where('proposals.status','=','approved')
            ->orderBy('proposals.id', 'DESC')
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
            $criteria = DB::table('proposals')
            ->join('students','proposals.id_student','students.id')
            ->join('lecturers','proposals.id_lecturer','lecturers.id')
            ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('proposals.id_lecturer','=',$id)
            ->where('proposals.status','=','revise')
            ->orderBy('proposals.id', 'DESC')
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
            $criteria = DB::table('proposals')
            ->join('students','proposals.id_student','students.id')
            ->join('lecturers','proposals.id_lecturer','lecturers.id')
            ->select('proposals.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('proposals.id_lecturer','=',$id)
            ->where('proposals.status','=','waiting')
            ->orderBy('proposals.id', 'DESC')
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
            $criteria = DB::table('reports')
            ->join('students','reports.id_student','students.id')
            ->join('lecturers','reports.id_lecturer','lecturers.id')
            ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('reports.id_lecturer','=',$id)
            ->where('reports.status','=','accepted')
            ->orderBy('reports.id', 'DESC')
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
            $criteria = DB::table('reports')
            ->join('students','reports.id_student','students.id')
            ->join('lecturers','reports.id_lecturer','lecturers.id')
            ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('reports.id_lecturer','=',$id)
            ->where('reports.status','=','revise')
            ->orderBy('reports.id', 'DESC')
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
            $criteria = DB::table('reports')
            ->join('students','reports.id_student','students.id')
            ->join('lecturers','reports.id_lecturer','lecturers.id')
            ->select('reports.*','students.name','students.major','students.nim','lecturers.name AS lecturer_name')
            ->where('reports.id_lecturer','=',$id)
            ->where('reports.status','=','waiting')
            ->orderBy('reports.id', 'DESC')
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

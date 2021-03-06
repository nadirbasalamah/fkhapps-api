<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Student;
use App\Lecturer;
use App\Admin;

class AuthController extends Controller
{
    public function loginStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|min:15|max:15', 
            'password' => 'required', 
        ]);
        $status = "error";
        $message = [];
        $data = null;
        $code = 401;
        $isStudentFound = true;

        if ($validator->fails()) { // fungsi untuk ngecek apakah validasi gagal
            // validasi gagal
            $errors = $validator->errors();
            $messages = [];
            $fields = [];
            $i = 0;
            foreach ($errors->all() as $msg) {
                array_push($messages,$msg);
                $fields[$i] = explode(" ",$messages[$i]);
                $message[$fields[$i][1]] = $messages[$i];
                $i++;
            }
        } else {
            if(is_numeric($request->nim)) {
                try {
                    $student = Student::where('nim', '=', $request->nim)->firstOrFail();
                } catch (\Throwable $th) {
                    $isStudentFound = false;
                }
                if ($isStudentFound) {
                    if (Hash::check($request->password, $student->password)) {
                        //generate token
                        $student->generateToken();
                        $status = 'success';
                        $message['success'] = 'Login sukses';
                        $data = $student->toArray();
                        $code = 200;
                    } else {
                        $message['password'] = "Login failed, invalid password";
                    }
                } else {
                        $message['nim'] = "Login failed, invalid NIM";
                }
            } else {
                $message['nim'] = "NIM may only contains number";
            }
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function loginLecturer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|min:16|max:16', 
            'password' => 'required', 
        ]);
        
        $status = "error";
        $message = [];
        $data = null;
        $code = 401;
        $isLecturerFound = true;

        if ($validator->fails()) { // fungsi untuk ngecek apakah validasi gagal
            // validasi gagal
            $errors = $validator->errors();
            $messages = [];
            $fields = [];
            $i = 0;
            foreach ($errors->all() as $msg) {
                array_push($messages,$msg);
                $fields[$i] = explode(" ",$messages[$i]);
                $message[$fields[$i][1]] = $messages[$i];
                $i++;
            }
        } else {
            if(is_numeric(str_replace("P",0,$request->nip))) {
                try {
                    $lecturer = Lecturer::where('nip', '=', $request->nip)->firstOrFail();
                } catch (\Throwable $th) {
                    $isLecturerFound = false;
                }
                if ($isLecturerFound) {
                    if (Hash::check($request->password, $lecturer->password)) {
                        //generate token
                        $lecturer->generateToken();
                        $status = 'success';
                        $message['success'] = 'Login sukses';
                        $data = $lecturer->toArray();
                        $code = 200;
                    } else {
                        $message['password'] = "Login failed, invalid password";
                    }
                } else {
                    $message['nip'] = "Login failed, invalid NIP";
                }
            } else {
                $message['nip'] = "NIP may only contains number";
            }
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    public function loginAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required', 
            'password' => 'required', 
        ]);
        $status = "error";
        $message = [];
        $data = null;
        $code = 401;
        $isAdminFound = true;

        if ($validator->fails()) { // fungsi untuk ngecek apakah validasi gagal
            // validasi gagal
            $errors = $validator->errors();
            $messages = [];
            $fields = [];
            $i = 0;
            foreach ($errors->all() as $msg) {
                array_push($messages,$msg);
                $fields[$i] = explode(" ",$messages[$i]);
                $message[$fields[$i][1]] = $messages[$i];
                $i++;
            }
        } else {
                try {
                    $admin = Admin::where('name', '=', $request->name)->firstOrFail();
                } catch (\Throwable $th) {
                    $isAdminFound = false;
                }
                if ($isAdminFound) {
                    if (Hash::check($request->password, $admin->password)) {
                        //generate token
                        $admin->generateToken();
                        $status = 'success';
                        $message['success'] = 'Login sukses';
                        $data = $admin->toArray();
                        $code = 200;
                    } else {
                        $message['password'] = "Login failed, invalid password";
                    }
                } else {
                        $message['name'] = "Login failed, invalid username";
                }   
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function registerStudent(Request $request)
    {
    $validator = Validator::make($request->all(), [
    'nim' => 'required|string|regex:/^[0-9 .\-]+$/i|min:15|max:15', // nim harus diisi teks dengan panjang maksimal 15
    'name' => 'required|string|regex:/^[a-z .\-]+$/i',
    'email' => 'required|email',
    'major' => 'required|string|regex:/^[a-z .\-]+$/i',
    'study_program' => 'required|string|regex:/^[a-z .\-]+$/i',
    'academic_year' => 'required|integer',
    'password' => 'required|string|min:6', // password minimal 6 karakter
    ]);

    $status = "error";
    $message = [];
    $data = null;
    $code = 400;
    $lecturerFound = true;

    if ($validator->fails()) { // fungsi untuk ngecek apakah validasi gagal
        // validasi gagal
        $errors = $validator->errors();
        $messages = [];
        $fields = [];
        $i = 0;
            foreach ($errors->all() as $msg) {
                array_push($messages,$msg);
                $fields[$i] = explode(" ",$messages[$i]);
                $message[$fields[$i][1]] = $messages[$i];
                $i++;
            }
    }
    else{
            $student = Student::create([
                'nim' => $request->nim,
                'name' => $request->name,
                'email' => $request->email,
                'major' => $request->major,
                'study_program' => $request->study_program,
                'academic_year' => $request->academic_year,
                'password' => Hash::make($request->password),
            ]);
            if($student){
                $student->generateToken();
                $status = "success";
                $message['success'] = "register successfully";
                $data = $student->toArray();
                $code = 200;
            }
            else{
                $message['nim'] = 'register failed, nim already exist';
            }
    }

    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ], $code);
    }

    public function registerLecturer(Request $request)
    {
    $validator = Validator::make($request->all(), [
    'nip' => 'required|string|min:15|max:15', // nip harus diisi teks dengan panjang maksimal 15
    'name' => 'required|string',
    'email' => 'required|email',
    'password' => 'required|string|min:6', // password minimal 6 karakter
    ]);

    $status = "error";
    $message = [];
    $data = null;
    $code = 400;

    if ($validator->fails()) { // fungsi untuk ngecek apakah validasi gagal
        // validasi gagal
        $errors = $validator->errors();
        $messages = [];
        $fields = [];
        $i = 0;
            foreach ($errors->all() as $msg) {
                array_push($messages,$msg);
                $fields[$i] = explode(" ",$messages[$i]);
                $message[$fields[$i][1]] = $messages[$i];
                $i++;
            }
    }
    else{
        if(is_numeric($request->nip) && ctype_alpha(str_replace(' ','',$request->name))) {
            // validasi sukses
            $lecturer = Lecturer::create([
                'nip' => 'P'. $request->nip,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            if($lecturer){
                $lecturer->generateToken();
                $status = "success";
                $message['success'] = "register successfully";
                $data = $lecturer->toArray();
                $code = 200;
            }
            else{
                $message['error'] = 'register failed';
            }
        } else {
            if(!is_numeric($request->nip)) {
                $message['nip'] = 'NIP may only contains number';    
            } else {
                $message['name'] = 'name may only contains alphabetical characters';
            }
            
        }
    }

    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ], $code);

    }
    public function logoutStudent(Request $request)
    {
    $student = Auth::user();
    if ($student) {
    $student->api_token = null;
    $student->save();
    }
    return response()->json([
    'status' => 'success',
    'message' => 'logout success',
    'data' => null
    ], 200);
    }

    public function logoutLecturer(Request $request)
    {
    $lecturer = Auth::user();
    if ($lecturer) {
    $lecturer->api_token = null;
    $lecturer->save();
    }
    return response()->json([
    'status' => 'success',
    'message' => 'logout success',
    'data' => null
    ], 200);
    }
    
    public function logoutAdmin(Request $request)
    {
    $admin = Auth::user();
    if ($admin) {
    $admin->api_token = null;
    $admin->save();
    }
    return response()->json([
    'status' => 'success',
    'message' => 'logout success',
    'data' => null
    ], 200);
    }

}
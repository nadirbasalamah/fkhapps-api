<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Student;
use App\Lecturer;

class AuthController extends Controller
{
    public function loginStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|min:15|max:15', 
            'password' => 'required', 
        ]);
        $status = "error";
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) { // fungsi untuk ngecek apakah validasi gagal
            // validasi gagal
            $errors = $validator->errors();
            $message = $errors;
        } else {
            if(is_numeric($request->nim)) {
                $student = Student::where('nim', '=', $request->nim)->firstOrFail();
                if ($student) {
                    if (Hash::check($request->password, $student->password)) {
                        //generate token
                        $student->generateToken();
                        $status = 'success';
                        $message = 'Login sukses';
                        $data = $student->toArray();
                        $code = 200;
                    } else {
                        $message = "Login gagal, password salah";
                    }
                } else {
                        $message = "Login gagal, username salah";
                }
            } else {
                $message = "NIM may only contains number";
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
        $message = "";
        $data = null;
        $code = 401;

        if ($validator->fails()) { // fungsi untuk ngecek apakah validasi gagal
            // validasi gagal
            $errors = $validator->errors();
            $message = $errors;
        } else {
            if(is_numeric(str_replace("P",0,$request->nip))) {
                $lecturer = Lecturer::where('nip', '=', $request->nip)->firstOrFail();
                if ($lecturer) {
                    if (Hash::check($request->password, $lecturer->password)) {
                        //generate token
                        $lecturer->generateToken();
                        $status = 'success';
                        $message = 'Login sukses';
                        $data = $lecturer->toArray();
                        $code = 200;
                    } else {
                        $message = "Login gagal, password salah";
                    }
                } else {
                    $message = "Login gagal, username salah";
                }
            } else {
                $message = "NIP may only contains number";
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
    'nip' => 'required|string|regex:/^[0-9 .\-]+$/i|min:15|max:15', // nip harus diisi teks dengan panjang maksimal 15
    'password' => 'required|string|min:6', // password minimal 6 karakter
    ]);

    $status = "error";
    $message = "";
    $data = null;
    $code = 400;

    if ($validator->fails()) { // fungsi untuk ngecek apakah validasi gagal
        // validasi gagal
        $errors = $validator->errors();
        $message = $errors;
    }
    else{
        // validasi sukses
            $lecturer = Lecturer::where('nip', '=', 'P' . $request->nip)->firstOrFail();
            if($lecturer) {
                $student = Student::create([
                    'nim' => $request->nim,
                    'name' => $request->name,
                    'email' => $request->email,
                    'major' => $request->major,
                    'study_program' => $request->study_program,
                    'academic_year' => $request->academic_year,
                    'nip' => $request->nip,
                    'password' => Hash::make($request->password),
                ]);
                if($student){
                    $student->generateToken();
                    $status = "success";
                    $message = "register successfully";
                    $data = $student->toArray();
                    $code = 200;
                }
                else{
                    $message = 'register failed, nim already exist';
                }
    
            } else {
                $message = 'register failed, nip not found';
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
    'nip' => 'required|string|min:15|max:15', // nim harus diisi teks dengan panjang maksimal 15
    'name' => 'required|string',
    'email' => 'required|email',
    'password' => 'required|string|min:6', // password minimal 6 karakter
    ]);

    $status = "error";
    $message = "";
    $data = null;
    $code = 400;

    if ($validator->fails()) { // fungsi untuk ngecek apakah validasi gagal
        // validasi gagal
        $errors = $validator->errors();
        $message = $errors;
    }
    else{
        if(is_numeric($request->nip) && ctype_alpha(str_replace(' ','',$request->name))) {
            // validasi sukses
            $lecturer = Lecturer::create([
                'nip' => 'P'. $request->nip,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            if($lecturer){
                $lecturer->generateToken();
                $status = "success";
                $message = "register successfully";
                $data = $lecturer->toArray();
                $code = 200;
            }
            else{
                $message = 'register failed';
            }
        } else {
            $message = 'NIP may only contains number';
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
    'message' => 'logout berhasil',
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
    'message' => 'logout berhasil',
    'data' => null
    ], 200);
    }

}
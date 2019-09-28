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
        $this->validate($request, [
            'nim' => 'required',
            'password' => 'required',
        ]);

        $student = Student::where('nim', '=', $request->nim)->firstOrFail();
        $status = "error";
        $message = "";
        $data = null;
        $code = 401;
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

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function loginLecturer(Request $request)
    {
        $this->validate($request, [
            'nip' => 'required',
            'password' => 'required',
        ]);

        $lecturer = Lecturer::where('nip', '=', $request->nip)->firstOrFail();
        $status = "error";
        $message = "";
        $data = null;
        $code = 401;
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

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function registerStudent(Request $request)
    {
    $validator = Validator::make($request->all(), [
    'nim' => 'required|string|min:15', // nim harus diisi teks dengan panjang maksimal 15
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
        $student = Student::create([
            'nim' => $request->nim,
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
            $message = 'register failed';
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
    'nip' => 'required|string|min:15', // nim harus diisi teks dengan panjang maksimal 15
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
        $lecturer = Lecturer::create([
            'nip' => $request->nip,
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
    }

    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ], $code);

    }
    public function logoutStudent(Request $request)
    {
    $student = Auth::student();
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
    $lecturer = Auth::lecturer();
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
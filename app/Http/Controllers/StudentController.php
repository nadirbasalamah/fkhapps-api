<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Lecturers as LecturerResource;
use Illuminate\Support\Facades\Auth;
use App\Lecturer;

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
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Proposal;
use App\Report;

class LecturerController extends Controller
{
    public function verifyProposal(Request $request, $id)
    {
        $message = "";
        $data = [];
        $code = 400;
        $lecturer = Auth::user();
        if ($lecturer) {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors;
            } else {
                $proposal = Proposal::find($id);
                $proposal->status = $request->status;
                $proposal->save();
                $message = 'proposal status updated!';
                $code = 200;
                $data = $proposal;
            }
        } else {
            $message = "Error, access not allowed";
            $code = 403;
        }
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
            ], $code);
    }

    public function verifyReport(Request $request, $id)
    {
        $message = "";
        $data = [];
        $code = 400;
        $lecturer = Auth::user();
        if ($lecturer) {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors;
            } else {
                $report = Report::find($id);
                $report->status = $request->status;
                $report->save();
                $message = 'report status updated!';
                $code = 200;
                $data = $report;
            }
        } else {
            $message = "Error, access not allowed";
            $code = 403;
        }
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
            ], $code);
    }
}
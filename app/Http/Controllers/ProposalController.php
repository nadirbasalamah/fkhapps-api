<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Proposals as ProposalResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Proposal;

class ProposalController extends Controller
{
    public function getAllProposal()
    {
        $criteria = Proposal::paginate(6);
        return new ProposalResource($criteria);
    }

    public function getAllProposalsByLecturerId(Request $request)
    {
        $lecturer = Auth::user();
        $status = "error";
        $message = "";
        $data = [];
    if($lecturer){
        $proposals = Proposal::select('*')
        ->where('id_lecturer','=',$lecturer->id)
        ->orderBy('id','DESC')
        ->get();
        $status = "success";
        $message = "data of proposals";
        $data = $proposals;
    }
    else {
        $message = "Proposal not found";
    }
        return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
        ], 200);
    }

    public function getProposalByStudentId($id)
    {
        $criteria = Proposal::select('*')
        ->where('id_student','=',$id)
        ->orderBy('id', 'DESC')
        ->get();
        return new ProposalResource($criteria);
    }

    public function getProposalByTitle($title)
    {
        $criteria = Proposal::select('*')
        ->where('title', 'LIKE', "%".$title."%")
        ->orderBy('id', 'DESC')
        ->get();
        return new ProposalResource($criteria);
    }

    public function uploadProposal(Request $request)
    {
        $student = Auth::user();
        $status = "error";
        $message = "";
        $data = [];
    if($student){
        //TODO: upload proposal
    }
    else {
        $message = "Error, access not allowed";
    }
        return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
        ], 200);
    }

}
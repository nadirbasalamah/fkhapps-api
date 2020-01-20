<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Proposals as ProposalResource;
use Illuminate\Support\Facades\Auth;
use App\Proposal;

class ProposalController extends Controller
{
    public function getAllProposal()
    {
        $criteria = Proposal::paginate(6);
        return new ProposalResource($criteria);
    }

    public function getAllProposalsByLecturerId(Request $request, $id)
    {
        $lecturer = Auth::user();
        $status = "error";
        $message = "";
        $data = [];
    if($lecturer){
        $proposals = Proposal::select('*')
        ->where('id_lecturer','=',$id)
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

    public function getProposalById($id)
    {
        $criteria = Proposal::select('*')
        ->where('id','=',$id)
        ->orderBy('id', 'DESC')
        ->get();
        return new ProposalResource($criteria);
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
}
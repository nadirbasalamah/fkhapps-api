<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'id_student','id_lecturer','course','title','research_background','research_question','filename','result_case','result_place',
        'result_time','status','notes','revision','final','finalfile'
    ];

    public function student()
    {
        return $this->belongsTo("App\Student");
    }

    public function lecturer()
    {
        return $this->belongsTo("App\Lecturer");
    }
}
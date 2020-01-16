<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'id_student','id_lecturer','title','research_background','research_question','filename','status','notes'
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

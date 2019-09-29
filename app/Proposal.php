<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_student','id_lecturer','title','research_background','research_question','filename','status'
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
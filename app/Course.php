<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_student','name','access','nip','lecturer_name'
    ];

    public function student()
    {
        return $this->belongsTo("App\Student");
    }
}
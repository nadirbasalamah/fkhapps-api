<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lecturer_course extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'lecturer_courses'; 
     
    protected $fillable = [
        'id_lecturer','name'
    ];
}
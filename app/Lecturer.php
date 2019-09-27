<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nip','password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function generateToken()
    {
    $this->api_token = str_random(60);
    $this->save();
    return $this->api_token;
    }
}
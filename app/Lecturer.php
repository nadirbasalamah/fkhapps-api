<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nip','name','email','password'
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
    $this->api_token = Str::random(60);
    $this->save();
    return $this->api_token;
    }

    public function proposal()
    {
        return $this->hasOne("App\Proposal");
    }
    
}
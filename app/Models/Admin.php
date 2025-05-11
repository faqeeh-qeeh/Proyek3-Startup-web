<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'full_name',
        'username',
        'email',
        'nim',
        'gender',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
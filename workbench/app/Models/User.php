<?php

namespace Workbench\App\Models;

use Illuminate\Foundation\Auth\User as Authenticable;

class User extends Authenticable
{
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'profile',
    ];

    protected $casts = [
        'profile' => 'array',
    ];
}
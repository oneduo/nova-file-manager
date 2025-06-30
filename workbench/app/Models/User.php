<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Foundation\Auth\User as Authenticable;
use Oneduo\NovaFileManager\Casts\Asset;

class User extends Authenticable
{
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'profile',
        'simple_image',
        'image',
    ];

    protected $casts = [
        'profile' => 'array',
        'image' => Asset::class,
    ];
}

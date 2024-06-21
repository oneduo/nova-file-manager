<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Oneduo\NovaFileManager\Casts\Asset;
use Whitecube\NovaFlexibleContent\Concerns\HasFlexible;
use Whitecube\NovaFlexibleContent\Value\FlexibleCast;

class Page extends Model
{
    use HasFlexible;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'content',
    ];

    protected $casts = [
        'image' => Asset::class,
        'content' => FlexibleCast::class,
    ];
}
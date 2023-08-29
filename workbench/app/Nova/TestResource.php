<?php

declare(strict_types=1);

namespace Workbench\App\Nova;

use Illuminate\Foundation\Auth\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Oneduo\NovaFileManager\FileManager;

class TestResource extends Resource
{
    public static $model = User::class;

    public function fields(NovaRequest $request): array
    {
        return [
            FileManager::make('Image'),
        ];
    }
}

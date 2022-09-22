<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Tests\Fixture;

use Oneduo\NovaFileManager\FileManager;
use Illuminate\Foundation\Auth\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

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

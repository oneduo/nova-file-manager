<?php

declare(strict_types=1);

namespace Workbench\App\Nova;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Oneduo\NovaFileManager\FileManager;

class TestResourceWithOnDemandFilesystem extends Resource
{
    public static $model = User::class;

    public function fields(NovaRequest $request): array
    {
        return [
            FileManager::make('Image')
                ->multiple()
                ->filesystem(function (NovaRequest $request) {
                    return Storage::build([
                        'driver' => 'local',
                        'root' => storage_path('app//public/users/'.$request->user()->getKey()),
                        'url' => config('app.url').'/storage/users/'.$request->user()->getKey(),
                        'visibility' => 'public',
                    ]);
                }),
        ];
    }
}

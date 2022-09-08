<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Tests\Fixture;

use BBSLab\NovaFileManager\FileManager;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class TestResourceWithOnDemandFilesystem extends Resource
{
    public static $model = User::class;

    public function fields(NovaRequest $request): array
    {
        return [
            FileManager::make('Image')
                ->filesystem(function (NovaRequest $request) {
                    return Storage::build([
                        'driver' => 'local',
                        'root' => storage_path('framework/testing/disks/public/users/'.$request->user()->getKey()),
                        'url' => config('app.url').'/storage/users/'.$request->user()->getKey(),
                        'visibility' => 'public',
                    ]);
                }),
        ];
    }
}

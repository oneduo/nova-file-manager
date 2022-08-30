<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Tests;

use BBSLab\NovaFileManager\NovaFileManager;
use BBSLab\NovaFileManager\ToolServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Orchestra\Testbench\TestCase as Orchestra;
use Pion\Laravel\ChunkUpload\Providers\ChunkUploadServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            'Inertia\ServiceProvider',
            //            'Laravel\Nova\NovaCoreServiceProvider',
            ChunkUploadServiceProvider::class,
            ToolServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        Route::middlewareGroup('nova', []);
        Route::middlewareGroup('nova:api', []);
//
        Gate::define('viewNova', function ($user) {
            return true;
        });
//
        Nova::tools([
            NovaFileManager::make(),
        ]);
    }
}

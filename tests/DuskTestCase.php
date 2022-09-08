<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Tests;

use BBSLab\NovaFileManager\NovaFileManager;
use BBSLab\NovaFileManager\ToolServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\NovaCoreServiceProvider;
use Laravel\Nova\NovaServiceProvider;
use NovaKit\NovaPackagesTool\LaravelServiceProvider;
use Orchestra\Testbench\Dusk\TestCase as BaseDuskTestCase;
use Pion\Laravel\ChunkUpload\Providers\ChunkUploadServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;

class DuskTestCase extends BaseDuskTestCase
{
    use DatabaseMigrations;

    protected function getPackageProviders($app): array
    {
        return [
            \Inertia\ServiceProvider::class,
            ChunkUploadServiceProvider::class,
            LaravelServiceProvider::class,
            NovaCoreServiceProvider::class,
            NovaApplicationServiceProvider::class,
            NovaServiceProvider::class,
            RayServiceProvider::class,
            ToolServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('nova:publish');
        $this->artisan('ray:publish-config');
    }

    protected function setUpDuskServer(): void
    {
        parent::setUpDuskServer();

        File::ensureDirectoryExists(static::applicationBasePath().'/app/Nova');

        Auth::setUser((new User())->forceFill(['id' => 42]));

        Route::middlewareGroup('nova:api', ['nova']);

        Gate::define('viewNova', function ($user) {
            return true;
        });

        Nova::tools([
            NovaFileManager::make(),
        ]);
    }

    protected function resolveApplication()
    {
        return tap(new Application($this->getBasePath()), function ($app) {
            $app->detectEnvironment(function () {
                return 'local';
            });
        });
    }
}

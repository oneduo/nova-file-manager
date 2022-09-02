<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager;

use BBSLab\NovaFileManager\Contracts\Filesystem\Upload\Uploader as UploaderContract;
use BBSLab\NovaFileManager\Contracts\Services\FileManagerContract;
use BBSLab\NovaFileManager\Filesystem\Upload\Uploader;
use BBSLab\NovaFileManager\Http\Middleware\Authorize;
use BBSLab\NovaFileManager\Services\FileManagerService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'nova-file-manager');
        $this->mergeConfigFrom(__DIR__.'/../config/nova-file-manager.php', 'nova-file-manager');

        $this->publishes([
            __DIR__.'/../config/nova-file-manager.php' => config_path('nova-file-manager.php'),
        ], 'config');

        Nova::serving(static function (ServingNova $event) {
            Nova::translations(__DIR__.'/../lang/en.json');
            Nova::script('nova-file-manager', __DIR__.'/../dist/js/tool.js');
            Nova::style('nova-file-manager', __DIR__.'/../dist/css/tool.css');
        });

        Inertia::version(static function () {
            return md5_file(__DIR__.'../dist/mix-manifest.json');
        });

        $this->publish();
    }

    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authorize::class], 'nova-file-manager')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova:api', Authorize::class])
            ->prefix('nova-vendor/nova-file-manager')
            ->group(__DIR__.'/../routes/api.php');
    }

    public function register(): void
    {
        $this->app->singleton(UploaderContract::class, Uploader::class);
        $this->app->singleton(FileManagerContract::class, function (Application $app, array $args = []) {
            /** @var \Illuminate\Http\Request $request */
            $request = $app->make('request');

            $disk = $args['disk'] ?? $request->input('disk');
            $path = $args['path'] ?? $request->input('path', DIRECTORY_SEPARATOR);
            $page = (int) ($args['page'] ?? $request->input('page', 1));
            $perPage = (int) ($args['perPage'] ?? $request->input('perPage', 15));
            $search = $args['search'] ?? $request->input('search');

            return FileManagerService::make($disk, $path, $page, $perPage, $search);
        });
    }

    public function publish(): void
    {
        if ($this->app->runningInConsole()) {
            $this->mergeConfigFrom(__DIR__.'/../config/nova-file-manager.php', 'nova-file-manager');

            $this->publishes(
                [
                    __DIR__.'/../config' => config_path(),
                ],
                'nova-file-manager-config'
            );
        }
    }
}

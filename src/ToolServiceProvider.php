<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;
use Oneduo\NovaFileManager\Contracts\Filesystem\Upload\Uploader as UploaderContract;
use Oneduo\NovaFileManager\Contracts\Services\FileManagerContract;
use Oneduo\NovaFileManager\Filesystem\Upload\Uploader;
use Oneduo\NovaFileManager\Http\Middleware\Authorize;
use Oneduo\NovaFileManager\Services\FileManagerService;
use Spatie\LaravelImageOptimizer\Middlewares\OptimizeImages;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the tool services
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        $this->config();
        $this->translations();

        Nova::serving(function () {
            $this->loadTranslationsToNova();
            $this->assets();
        });
    }

    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router([
            'nova', Authenticate::class, Authorize::class
        ], 'nova-file-manager')
            ->group(__DIR__ . '/../routes/inertia.php');

        Route::middleware(['nova:api', Authorize::class])
            ->prefix('nova-vendor/nova-file-manager')
            ->group(__DIR__ . '/../routes/api.php');
    }

    public function register(): void
    {
        $this->app->singleton(UploaderContract::class, Uploader::class);

        $this->app->singleton(FileManagerContract::class, function (Application $app, array $args = []) {
            /** @var \Illuminate\Http\Request $request */
            $request = $app->make('request');

            $disk = $args['disk'] ?? $request->input('disk');
            $path = $args['path'] ?? $request->input('path', DIRECTORY_SEPARATOR);
            $page = (int)($args['page'] ?? $request->input('page', 1));
            $perPage = (int)($args['perPage'] ?? $request->input('perPage', 15));
            $search = $args['search'] ?? $request->input('search');

            return FileManagerService::make($disk, $path, $page, $perPage, $search);
        });
    }

    public function config(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/nova-file-manager.php', 'nova-file-manager');

        $this->publishes(
            [
                __DIR__ . '/../config' => config_path(),
            ],
            'nova-file-manager-config'
        );
    }

    protected function translations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'nova-file-manager');
        $this->loadJsonTranslationsFrom(__DIR__ . '/../lang');
    }

    public function assets(): void
    {
        Nova::style('nova-file-manager', __DIR__ . '/../dist/css/tool.css');
        Nova::script('nova-file-manager', __DIR__ . '/../dist/js/tool.js');
    }

    public function loadTranslationsToNova(): void
    {
        $translations = trans('nova-file-manager::ui');

        if (!is_array($translations)) {
            $translations = [];
        }

        $translations = array_merge(trans('nova-file-manager::ui', [], 'en'), $translations);

        Nova::translations($translations);
    }
}

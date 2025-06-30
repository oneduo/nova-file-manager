<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Oneduo\NovaFileManager\FileManager;
use Oneduo\NovaFileManager\NovaFileManager;
use Outl1ne\NovaSettings\NovaSettings;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        NovaSettings::addSettingsFields([
            Text::make('Some setting', 'some_setting'),
            Number::make('A number', 'a_number'),
            FileManager::make('Image', 'image')
                ->wrapper('simple'),
        ]);

        FileManager::registerWrapper('simple', function (FileManager $field) {
            return $field
                ->simple(filesystem: function (NovaRequest $request) {
                    return Storage::build([
                        'driver' => 'local',
                        'root' => storage_path('app/public/users/'.$request->user()->getKey()),
                        'url' => config('app.url').'/storage/users/'.$request->user()->getKey(),
                        'visibility' => 'public',
                    ]);
                });
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return true;
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \Laravel\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            NovaSettings::make(),
            NovaFileManager::make()
                ->pagination(function (NovaRequest $request) {
                    return [10, 42, 84];
                })
                ->perPage(fn () => 42),
        ];
    }

    /**
     * Register the application's Nova resources.
     *
     * @return void
     */
    protected function resources()
    {
        Nova::resources([
            \Workbench\App\Nova\User::class,
            \Workbench\App\Nova\TestResource::class,
            \Workbench\App\Nova\TestResourceWithOnDemandFilesystem::class,
            \Workbench\App\Nova\Page::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        FileManager::registerWrapper('repeater', function (FileManager $field) {
            return $field
                // some options
                ->filesystem(fn () => 'public');
        });
    }
}

<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Nova\Http\Middleware\BootTools;
use Laravel\Nova\Http\Middleware\DispatchServingNovaEvent;
use Laravel\Nova\Http\Middleware\HandleInertiaRequests;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Oneduo\NovaFileManager\NovaFileManager;

use function Pest\Laravel\actingAs;

it('can show the file manager tool', function () {
    Route::middlewareGroup('nova', [
        'web',
        HandleInertiaRequests::class,
        DispatchServingNovaEvent::class,
        BootTools::class,
    ]);

    actingAs(new User())
        ->get(route('nova-file-manager.tool'))
        ->assertInertia(function (Assert $page) {
            $page
                ->component('NovaFileManager', false)
                ->where('config.upload', null)
                ->where('config.singleDisk', false)
                ->where('config.permissions', [
                    'file' => [
                        'delete' => true,
                        'download' => true,
                        'edit' => true,
                        'rename' => true,
                        'unzip' => true,
                        'upload' => true,
                    ],
                    'folder' => [
                        'create' => true,
                        'delete' => true,
                        'rename' => true,
                    ],
                ])
                ->where('config.paginationOptions', range(
                    10,
                    50,
                    10
                ));
        });
});

it('can show the file manager tool with custom pagination', function () {
    Nova::$tools = [
        NovaFileManager::make()->pagination(function (NovaRequest $request) {
            return [10, 42, 84];
        }),
    ];

    Route::middlewareGroup('nova', [
        'web',
        HandleInertiaRequests::class,
        DispatchServingNovaEvent::class,
        BootTools::class,
    ]);

    actingAs(new User())
        ->get(route('nova-file-manager.tool'))
        ->assertInertia(function (Assert $page) {
            $page
                ->component('NovaFileManager', false)
                ->where('config.upload', null)
                ->where('config.singleDisk', false)
                ->where('config.permissions', [
                    'file' => [
                        'delete' => true,
                        'download' => true,
                        'edit' => true,
                        'rename' => true,
                        'unzip' => true,
                        'upload' => true,
                    ],
                    'folder' => [
                        'create' => true,
                        'delete' => true,
                        'rename' => true,
                    ],
                ])
                ->where('config.paginationOptions', [10, 42, 84]);
        });
});

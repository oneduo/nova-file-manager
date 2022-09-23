<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Nova\Http\Middleware\BootTools;
use Laravel\Nova\Http\Middleware\DispatchServingNovaEvent;
use Laravel\Nova\Http\Middleware\HandleInertiaRequests;
use function Pest\Laravel\get;

it('can show the file manager tool', function () {
    Route::middlewareGroup('nova', [
        HandleInertiaRequests::class,
        DispatchServingNovaEvent::class,
        BootTools::class,
    ]);

    get(route('nova-file-manager.tool'))
        ->assertInertia(function (Assert $page) {
            $page
                ->component('NovaFileManager', false)
                ->where('config.upload', null)
                ->where('config.singleDisk', false)
                ->where('config.permissions', [
                    'file' => [
                        'delete' => true,
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
                ]);
        });
});

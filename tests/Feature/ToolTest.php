<?php

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
            ray($page);
            $page
                ->component('NovaFileManager', false)
                ->where('config.upload', null)
                ->where('config.customDisk', false)
                ->where('config.permissions', [
                    'showCreateFolder' => true,
                    'showRenameFolder' => true,
                    'showDeleteFolder' => true,
                    'showUploadFile' => true,
                    'showRenameFile' => true,
                    'showDeleteFile' => true,
                    'showCropImage' => true,
                ]);
        });
});
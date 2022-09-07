<?php

declare(strict_types=1);

use BBSLab\NovaFileManager\FileManager;
use BBSLab\NovaFileManager\NovaFileManager;
use BBSLab\NovaFileManager\Tests\Fixture\TestResource;
use BBSLab\NovaFileManager\Tests\Fixture\TestResourceWithOnDemandFilesystem;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->disk = 'public';
    $this->user = (new User())->forceFill(['id' => 42]);
    Storage::fake($this->disk);
});

it('cannot retrieve files of undefined disk', function () {
    getJson(uri: route('nova-file-manager.data').'?'.Arr::query(['disk' => 'unknown']))
        ->assertJsonValidationErrors([
            'disk' => [
                __('validation.exists', ['attribute' => 'disk']),
            ],
        ]);
});

it('can retrieve files', function () {
    Storage::disk($this->disk)->put($path = Str::random().'.txt', Str::random());

    $response = getJson(uri: route('nova-file-manager.data').'?'.Arr::query(['disk' => $this->disk]));

    $response
        ->assertOk()
        ->assertJson([
            'disk' => $this->disk,
            'breadcrumbs' => [],
            'directories' => [],
            'files' => [
                [
                    'path' => $path,
                ],
            ],
            'pagination' => [
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 1,
                'total' => 1,
            ],
        ]);
});

it('can retrieve files from tool with a custom filesystem', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->filesystem(function (NovaRequest $request) {
                return Storage::build([
                    'driver' => 'local',
                    'root' => storage_path('framework/testing/disks/public/users/'.$request->user()->getKey()),
                    'url' => env('APP_URL').'/storage/users/'.$request->user()->getKey(),
                    'visibility' => 'public',
                ]);
            }),
    ];

    Storage::disk($this->disk)->put('users/42/'.($path = Str::random().'.txt'), Str::random());
    Storage::disk($this->disk)->put('users/84/'.(Str::random().'.txt'), Str::random());

    actingAs($this->user)
        ->getJson(uri: route('nova-file-manager.data'))
        ->assertOk()
        ->assertJson([
            'disk' => 'default',
            'breadcrumbs' => [],
            'directories' => [],
            'files' => [
                [
                    'path' => $path,
                ],
            ],
            'pagination' => [
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 1,
                'total' => 1,
            ],
        ]);
});

it('can retrieve files from field with a custom filesystem', function () {
    Nova::resources([
        TestResourceWithOnDemandFilesystem::class,
    ]);

    Storage::disk($this->disk)->put('users/42/'.($path = Str::random().'.txt'), Str::random());
    Storage::disk($this->disk)->put('users/84/'.(Str::random().'.txt'), Str::random());

    $query = Arr::query([
        'resource' => TestResourceWithOnDemandFilesystem::uriKey(),
        'resourceId' => null,
        'attribute' => 'image',
        'fieldMode' => 1,
    ]);

    actingAs($this->user)
        ->getJson(uri: route('nova-file-manager.data')."?{$query}")
        ->assertOk()
        ->assertJson([
            'disk' => 'default',
            'breadcrumbs' => [],
            'directories' => [],
            'files' => [
                [
                    'path' => $path,
                ],
            ],
            'pagination' => [
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 1,
                'total' => 1,
            ],
        ]);
});
it('cannot retrieve files from field with the wrong attribute', function () {
    Nova::resources([
        TestResource::class,
    ]);

    $query = Arr::query([
        'resource' => TestResource::uriKey(),
        'resourceId' => null,
        'attribute' => 'images',
        'fieldMode' => 1,
    ]);

    actingAs($this->user)
        ->getJson(uri: route('nova-file-manager.data')."?{$query}")
        ->assertNotFound();
});

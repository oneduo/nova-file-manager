<?php

declare(strict_types=1);

use BBSLab\NovaFileManager\Http\Requests\CreateFolderRequest;
use BBSLab\NovaFileManager\NovaFileManager;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

beforeEach(function () {
    $this->disk = 'public';
    $this->user = (new User())->forceFill(['id' => 42]);
    Storage::fake($this->disk);
});

it('can configure showCreateFolder to authorize user to create a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->showCreateFolder(function (NovaRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedCreateChecks();
});

it('can configure showCreateFolder to prevent user to create a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->showCreateFolder(function (NovaRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedCreateChecks();
});

it('can configure canCreateFolder to authorize user to create a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->canCreateFolder(function (NovaRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedCreateChecks();
});

it('can configure canCreateFolder to prevent user to create a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->canCreateFolder(function (NovaRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedCreateChecks();
});

test('canCreateFolder takes precedence over showCreateFolder', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->showCreateFolder(function (NovaRequest $request) {
                return $request->user()?->getKey() === 42;
            })
            ->canCreateFolder(function (CreateFolderRequest $request) {
                return str_contains($request->path, 'foo');
            }),
    ];

    $this->performUnauthorizedCreateChecks();
});

it('can throw a custom validation message using canCreateFolder', function () {
    $message = 'Folder must contains `foo`';

    Nova::$tools = [
        NovaFileManager::make()
            ->canCreateFolder(function (CreateFolderRequest $request) use ($message) {
                if (!str_contains($request->path, 'foo')) {
                    throw ValidationException::withMessages([
                        'folder' => [$message],
                    ]);
                }

                return true;
            }),
    ];

    $this->performUnauthorizedCreateChecks($message);
});

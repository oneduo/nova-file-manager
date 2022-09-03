<?php

declare(strict_types=1);

use BBSLab\NovaFileManager\Http\Requests\DeleteFolderRequest;
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

it('can configure showDeleteFolder to authorize user to delete a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->showDeleteFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedDeleteChecks();
});

it('can configure showDeleteFolder to prevent user to delete a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->showDeleteFolder(function (NovaRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedDeleteChecks();
});

it('can configure canDeleteFolder to authorize user to delete a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->canDeleteFolder(function (DeleteFolderRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedDeleteChecks();
});

it('can configure canDeleteFolder to prevent user to rename a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->canDeleteFolder(function (DeleteFolderRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedDeleteChecks();
});

test('canDeleteFolder takes precedence over showDeleteFolder', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->showDeleteFolder(function (NovaRequest $request) {
                return $request->user()?->getKey() === 42;
            })
            ->canDeleteFolder(function (DeleteFolderRequest $request) {
                return str_contains($request->path, 'foo');
            }),
    ];

    $this->performUnauthorizedDeleteChecks();
});

it('can throw a custom validation message using canDeleteFolder', function () {
    $message = 'Folder must contains `foo`';

    Nova::$tools = [
        NovaFileManager::make()
            ->canDeleteFolder(function (DeleteFolderRequest $request) use ($message) {
                if (!str_contains($request->path, 'foo')) {
                    throw ValidationException::withMessages([
                        'folder' => [$message],
                    ]);
                }

                return true;
            }),
    ];

    $this->performUnauthorizedDeleteChecks($message);
});

<?php

declare(strict_types=1);

use BBSLab\NovaFileManager\Http\Requests\RenameFolderRequest;
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

it('can configure showRenameFolder to authorize user to rename a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->showRenameFolder(function (NovaRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedRenameChecks();
});

it('can configure showRenameFolder to prevent user to rename a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->showRenameFolder(function (NovaRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedRenameChecks();
});

it('can configure canRenameFolder to authorize user to rename a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->canRenameFolder(function (RenameFolderRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedRenameChecks();
});

it('can configure canRenameFolder to prevent user to rename a directory', function () {
    Nova::$tools = [
        NovaFileManager::make()->canRenameFolder(function (RenameFolderRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedRenameChecks();
});

test('canRenameFolder takes precedence over showRenameFolder', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->showRenameFolder(function (NovaRequest $request) {
                return $request->user()?->getKey() === 42;
            })
            ->canRenameFolder(function (RenameFolderRequest $request) {
                return str_contains($request->newPath, 'foo');
            }),
    ];

    $this->performUnauthorizedRenameChecks();
});

it('can throw a custom validation message using canRenameFolder', function () {
    $message = 'Folder must contains `foo`';

    Nova::$tools = [
        NovaFileManager::make()
            ->canRenameFolder(function (RenameFolderRequest $request) use ($message) {
                if (!str_contains($request->newPath, 'foo')) {
                    throw ValidationException::withMessages([
                        'folder' => [$message],
                    ]);
                }

                return true;
            }),
    ];

    $this->performUnauthorizedRenameChecks($message);
});

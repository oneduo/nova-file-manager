<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Oneduo\NovaFileManager\Http\Requests\DeleteFileRequest;
use Oneduo\NovaFileManager\NovaFileManager;

beforeEach(function () {
    $this->disk = 'public';
    $this->user = (new User())->forceFill(['id' => 42]);
    Storage::fake($this->disk);
});

it('can configure showDeleteFile to authorize user to delete a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->showDeleteFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedDeleteChecks();
});

it('can configure showDeleteFile to prevent user to delete a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->showDeleteFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedDeleteChecks();
});

it('can configure canDeleteFile to authorize user to delete a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->canDeleteFile(function (DeleteFileRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedDeleteChecks();
});

it('can configure canDeleteFile to prevent user to rename a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->canDeleteFile(function (DeleteFileRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedDeleteChecks();
});

test('canDeleteFile takes precedence over showDeleteFile', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->showDeleteFile(function (NovaRequest $request) {
                return $request->user()?->getKey() === 42;
            })
            ->canDeleteFile(function (DeleteFileRequest $request) {
                return str_contains($request->path, 'foo');
            }),
    ];

    $this->performUnauthorizedDeleteChecks();
});

it('can throw a custom validation message using canDeleteFile', function () {
    $message = 'Folder must contains `foo`';

    Nova::$tools = [
        NovaFileManager::make()
            ->canDeleteFile(function (DeleteFileRequest $request) use ($message) {
                if (! str_contains($request->path, 'foo')) {
                    throw ValidationException::withMessages([
                        'file' => [$message],
                    ]);
                }

                return true;
            }),
    ];

    $this->performUnauthorizedDeleteChecks($message);
});

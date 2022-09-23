<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Oneduo\NovaFileManager\Http\Requests\RenameFileRequest;
use Oneduo\NovaFileManager\NovaFileManager;

beforeEach(function () {
    $this->disk = 'public';
    $this->user = (new User())->forceFill(['id' => 42]);
    Storage::fake($this->disk);
});

it('can configure showRenameFile to authorize user to rename a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->showRenameFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedRenameChecks();
});

it('can configure showRenameFile to prevent user to rename a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->showRenameFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedRenameChecks();
});

it('can configure canRenameFile to authorize user to rename a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->canRenameFile(function (RenameFileRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedRenameChecks();
});

it('can configure canRenameFile to prevent user to rename a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->canRenameFile(function (RenameFileRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedRenameChecks();
});

test('canRenameFile takes precedence over showRenameFile', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->showRenameFile(function (NovaRequest $request) {
                return $request->user()?->getKey() === 42;
            })
            ->canRenameFile(function (RenameFileRequest $request) {
                return str_contains($request->to, 'foo');
            }),
    ];

    $this->performUnauthorizedRenameChecks();
});

it('can throw a custom validation message using canRenameFile', function () {
    $message = 'Folder must contains `foo`';

    Nova::$tools = [
        NovaFileManager::make()
            ->canRenameFile(function (RenameFileRequest $request) use ($message) {
                if (!str_contains($request->to, 'foo')) {
                    throw ValidationException::withMessages([
                        'file' => [$message],
                    ]);
                }

                return true;
            }),
    ];

    $this->performUnauthorizedRenameChecks($message);
});

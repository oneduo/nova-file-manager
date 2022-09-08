<?php

declare(strict_types=1);

use BBSLab\NovaFileManager\Http\Requests\UploadFileRequest;
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

it('can configure showUploadFile to authorize user to upload a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->showUploadFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedUploadChecks();
});

it('can configure showUploadFile to prevent user to upload a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->showUploadFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedUploadChecks();
});

it('can configure canUploadFile to authorize user to upload a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->canUploadFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedUploadChecks();
});

it('can configure canUploadFile to prevent user to upload a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->canUploadFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedUploadChecks();
});

test('canUploadFile takes precedence over showUploadFile', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->showUploadFile(function (NovaRequest $request) {
                return $request->user()?->getKey() === 42;
            })
            ->canUploadFile(function (UploadFileRequest $request) {
                return str_contains($request->path, 'foo');
            }),
    ];

    $this->performUnauthorizedUploadChecks();
});

it('can throw a custom validation message using canUploadFile', function () {
    $message = 'Folder must contains `foo`';

    Nova::$tools = [
        NovaFileManager::make()
            ->canUploadFile(function (UploadFileRequest $request) use ($message) {
                if (!str_contains($request->path, 'foo')) {
                    throw ValidationException::withMessages([
                        'file' => [$message],
                    ]);
                }

                return true;
            }),
    ];

    $this->performUnauthorizedUploadChecks($message);
});

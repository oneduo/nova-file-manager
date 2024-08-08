<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Oneduo\NovaFileManager\Http\Requests\DownloadFileRequest;
use Oneduo\NovaFileManager\NovaFileManager;

beforeEach(function () {
    $this->disk = 'public';
    $this->user = (new User())->forceFill(['id' => 42]);
    Storage::fake($this->disk);
});

it('can configure showDownloadFile to authorize user to download a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->showDownloadFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedDownloadChecks();
});

it('can configure showDownloadFile to prevent user to download a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->showDownloadFile(function (NovaRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedDownloadChecks();
});

it('can configure canDownloadFile to authorize user to download a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->canDownloadFile(function (DownloadFileRequest $request) {
            return $request->user()?->getKey() === 42;
        }),
    ];

    $this->performAuthorizedDownloadChecks();
});

it('can configure canDownloadFile to prevent user to download a file', function () {
    Nova::$tools = [
        NovaFileManager::make()->canDownloadFile(function (DownloadFileRequest $request) {
            return $request->user()?->getKey() === 84;
        }),
    ];

    $this->performUnauthorizedDownloadChecks();
});

test('canDownloadFile takes precedence over showDownloadFile', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->showDownloadFile(function (NovaRequest $request) {
                return $request->user()?->getKey() === 42;
            })
            ->canDownloadFile(function (DownloadFileRequest $request) {
                return str_contains($request->path, 'foo');
            }),
    ];

    $this->performUnauthorizedDownloadChecks();
});

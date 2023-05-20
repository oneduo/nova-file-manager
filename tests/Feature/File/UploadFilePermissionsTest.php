<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Oneduo\NovaFileManager\Http\Requests\UploadFileRequest;
use Oneduo\NovaFileManager\NovaFileManager;
use function Pest\Laravel\actingAs;

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
                if (! str_contains($request->path, 'foo')) {
                    throw ValidationException::withMessages([
                        'file' => [$message],
                    ]);
                }

                return true;
            }),
    ];

    $this->performUnauthorizedUploadChecks($message);
});

it('can validate upload', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->validateUploadUsing(function (UploadFileRequest $request, UploadedFile $file, array $meta, bool $saving) {
                return str_contains($file->getClientOriginalName(), 'foo');
            }),
    ];

    actingAs($this->user)
        ->postJson(
            uri: route('nova-file-manager.files.upload'),
            data: [
                'disk' => $this->disk,
                'path' => '/',
                'file' => UploadedFile::fake()->image($path = 'image.jpeg'),
                'resumableFilename' => $path,
            ],
        )
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'file' => [__('nova-file-manager::errors.file.upload_validation')],
        ]);

    Storage::disk($this->disk)->assertMissing($path);
});

it('can throw a custom validation message using validateUploadUsing', function () {
    $message = 'File name must contains `foo`';

    Nova::$tools = [
        NovaFileManager::make()
            ->validateUploadUsing(function (UploadFileRequest $request, UploadedFile $file, array $meta, bool $saving) use ($message) {
                if (! str_contains($request->path, 'foo')) {
                    throw ValidationException::withMessages([
                        'file' => [$message],
                    ]);
                }

                return true;
            }),
    ];

    actingAs($this->user)
        ->postJson(
            uri: route('nova-file-manager.files.upload'),
            data: [
                'disk' => $this->disk,
                'path' => '/',
                'file' => UploadedFile::fake()->image($path = 'image.jpeg'),
                'resumableFilename' => $path,
            ],
        )
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'file' => [$message],
        ]);

    Storage::disk($this->disk)->assertMissing($path);
});

<?php

declare(strict_types=1);

use BBSLab\NovaFileManager\Contracts\FileManagerContract;
use BBSLab\NovaFileManager\Events\FileDeleted;
use BBSLab\NovaFileManager\Events\FileRenamed;
use BBSLab\NovaFileManager\Events\FileUploaded;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

beforeEach(function () {
    $this->disk = 'public';
    Storage::fake($this->disk);
});

it('can upload file', function () {
    Event::fake();
    Storage::fake('local');

    postJson(
        uri: route('nova-file-manager.files.upload'),
        data: [
            'disk' => $this->disk,
            'path' => '/',
            'file' => UploadedFile::fake()->image($path = 'image.jpeg'),
        ],
    )
        ->assertOk()
        ->assertJson([
            'message' => __('Uploaded successfully'),
        ]);

    Storage::disk($this->disk)->assertExists($path);

    Event::assertDispatched(
        event: FileUploaded::class,
        callback: fn (FileUploaded $event) => $event->disk === $this->disk && $event->path === $path,
    );
});

it('can rename a file', function () {
    Event::fake();

    Storage::disk($this->disk)->put($path = 'file.txt', Str::random());

    Storage::disk($this->disk)->assertExists($path);

    postJson(
        uri: route('nova-file-manager.files.rename'),
        data: [
            'disk' => $this->disk,
            'oldPath' => $path,
            'newPath' => $new = 'secret.txt',
        ],
    )
        ->assertOk();

    Storage::disk($this->disk)->assertMissing($path);
    Storage::disk($this->disk)->assertExists($new);

    Event::assertDispatched(
        event: FileRenamed::class,
        callback: fn (FileRenamed $event
        ) => $event->disk === $this->disk && $event->oldPath === $path && $event->newPath === $new,
    );
});

it('cannot rename a non existing file', function () {
    Event::fake();

    $path = 'file.txt';

    Storage::disk($this->disk)->assertMissing($path);

    postJson(
        uri: route('nova-file-manager.files.rename'),
        data: [
            'disk' => $this->disk,
            'oldPath' => $path,
            'newPath' => $new = 'secret.txt',
        ],
    )
        ->assertJsonValidationErrors([
            'oldPath' => [
                __('validation.exists', ['attribute' => 'old path']),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileRenamed::class,
        callback: fn (FileRenamed $event
        ) => $event->disk === $this->disk && $event->oldPath === $path && $event->newPath === $new,
    );
});

it('cannot rename a file to an existing name', function () {
    Event::fake();

    Storage::disk($this->disk)->put($fisrt = 'first.txt', Str::random());
    Storage::disk($this->disk)->put($second = 'second.txt', Str::random());

    Storage::disk($this->disk)->assertExists($fisrt);
    Storage::disk($this->disk)->assertExists($second);

    $response = postJson(
        uri: route('nova-file-manager.files.rename'),
        data: [
            'disk' => $this->disk,
            'oldPath' => $fisrt,
            'newPath' => $second,
        ],
    )
        ->assertJsonValidationErrors([
            'newPath' => [
                __('validation.exists', ['attribute' => 'new path']),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileRenamed::class,
        callback: function (FileRenamed $event) use ($fisrt, $second) {
            return $event->disk === $this->disk && $event->oldPath === $fisrt && $event->newPath === $second;
        },
    );
});

it('throws an exception if the filesystem cannot rename the file', function () {
    Event::fake();

    $mock = mock(FileManagerContract::class)->expect(
        rename: fn (string $oldPath, string $newPath) => false,
    );

    app()->instance(FileManagerContract::class, $mock);

    Storage::disk($this->disk)->put($path = 'file.txt', Str::random());

    Storage::disk($this->disk)->assertExists($path);

    postJson(
        uri: route('nova-file-manager.files.rename'),
        data: [
            'disk' => $this->disk,
            'oldPath' => $path,
            'newPath' => $new = 'secret.txt',
        ]
    )
        ->assertJsonValidationErrors([
            'oldPath' => [
                __('Could not rename file !'),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileRenamed::class,
        callback: function (FileRenamed $event) use ($path, $new) {
            return $event->disk === $this->disk && $event->oldPath === $path && $event->newPath === $new;
        },
    );
});

it('can delete a file', function () {
    Event::fake();

    Storage::disk($this->disk)->put($path = 'file.txt', Str::random());

    Storage::disk($this->disk)->assertExists($path);

    postJson(
        uri: route('nova-file-manager.files.delete'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ],
    )
        ->assertOk();

    Storage::disk($this->disk)->assertMissing($path);

    Event::assertDispatched(
        event: FileDeleted::class,
        callback: function (FileDeleted $event) use ($path) {
            return $event->disk === $this->disk && $event->path === $path;
        },
    );
});

it('can delete a non existing file', function () {
    Event::fake();

    $path = 'file.txt';

    Storage::disk($this->disk)->assertMissing($path);

    postJson(
        uri: route('nova-file-manager.files.delete'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ],
    )
        ->assertJsonValidationErrors([
            'path' => [
                __('validation.exists', ['attribute' => 'path']),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileDeleted::class,
        callback: function (FileDeleted $event) use ($path) {
            return $event->disk === $this->disk && $event->path === $path;
        },
    );
});

it('throw an exception if the filesystem cannot delete the file', function () {
    Event::fake();

    $mock = mock(FileManagerContract::class)->expect(
        delete: fn (string $path) => false,
    );

    app()->instance(FileManagerContract::class, $mock);

    Storage::disk($this->disk)->put($path = 'file.txt', Str::random());

    Storage::disk($this->disk)->assertExists($path);

    postJson(
        uri: route('nova-file-manager.files.delete'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ],
    )
        ->assertJsonValidationErrors([
            'path' => [
                __('Could not delete file !'),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileDeleted::class,
        callback: function (FileDeleted $event) use ($path) {
            return $event->disk === $this->disk && $event->path === $path;
        },
    );
});

it('can download file', function () {
    Storage::disk($this->disk)->put($path = 'file.txt', $content = Str::random());
    Storage::disk($this->disk)->assertExists($path);

    $response = getJson(
        uri: route('nova-file-manager.files.download').'?'.Arr::query([
            'disk' => $this->disk,
            'path' => $path,
        ]),
    )
        ->assertOk();

    expect($response->baseResponse)
        ->toBeInstanceOf(BinaryFileResponse::class)
        ->and($response->baseResponse->getFile()->getContent())
        ->toBe($content);
});

<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Oneduo\NovaFileManager\Contracts\Services\FileManagerContract;
use Oneduo\NovaFileManager\Events\FileDeleted;
use Oneduo\NovaFileManager\Events\FileRenamed;
use Oneduo\NovaFileManager\Events\FileUploaded;
use function Pest\Laravel\postJson;

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
            'resumableFilename' => $path,
        ],
    )
        ->assertOk()
        ->assertJson([
            'message' => __('nova-file-manager::messages.file.upload'),
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
            'from' => $path,
            'to' => $new = 'secret.txt',
        ],
    )
        ->assertOk();

    Storage::disk($this->disk)->assertMissing($path);
    Storage::disk($this->disk)->assertExists($new);

    Event::assertDispatched(
        event: FileRenamed::class,
        callback: fn (FileRenamed $event
        ) => $event->disk === $this->disk && $event->from === $path && $event->to === $new,
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
            'from' => $path,
            'to' => $new = 'secret.txt',
        ],
    )
        ->assertJsonValidationErrors([
            'from' => [
                __('nova-file-manager::validation.path.missing', ['path' => $path]),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileRenamed::class,
        callback: fn (FileRenamed $event
        ) => $event->disk === $this->disk && $event->from === $path && $event->to === $new,
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
            'from' => $fisrt,
            'to' => $second,
        ],
    )
        ->assertJsonValidationErrors([
            'to' => [
                __('nova-file-manager::validation.path.exists', ['path' => $second]),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileRenamed::class,
        callback: function (FileRenamed $event) use ($fisrt, $second) {
            return $event->disk === $this->disk && $event->from === $fisrt && $event->to === $second;
        },
    );
});

it('throws an exception if the filesystem cannot rename the file', function () {
    Event::fake();

    $mock = mock(FileManagerContract::class)->expect(
        rename: fn (string $from, string $to) => false,
        filesystem: fn () => Storage::disk($this->disk),
    );

    app()->instance(FileManagerContract::class, $mock);

    Storage::disk($this->disk)->put($path = 'file.txt', Str::random());

    Storage::disk($this->disk)->assertExists($path);

    postJson(
        uri: route('nova-file-manager.files.rename'),
        data: [
            'disk' => $this->disk,
            'from' => $path,
            'to' => $new = 'secret.txt',
        ]
    )
        ->assertJsonValidationErrors([
            'from' => [
                __('nova-file-manager::errors.file.rename'),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileRenamed::class,
        callback: function (FileRenamed $event) use ($path, $new) {
            return $event->disk === $this->disk && $event->from === $path && $event->to === $new;
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
                __('nova-file-manager::validation.path.missing', ['path' => $path]),
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
        filesystem: fn () => Storage::disk($this->disk),
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
                __('nova-file-manager::errors.file.delete'),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileDeleted::class,
        callback: function (FileDeleted $event) use ($path) {
            return $event->disk === $this->disk && $event->path === $path;
        },
    );
});

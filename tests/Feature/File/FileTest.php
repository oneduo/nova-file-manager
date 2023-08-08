<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Nova\Nova;
use Oneduo\NovaFileManager\Contracts\Services\FileManagerContract;
use Oneduo\NovaFileManager\Events\FileDeleted;
use Oneduo\NovaFileManager\Events\FileDeleting;
use Oneduo\NovaFileManager\Events\FileRenamed;
use Oneduo\NovaFileManager\Events\FileRenaming;
use Oneduo\NovaFileManager\Events\FileUnzipped;
use Oneduo\NovaFileManager\Events\FileUnzipping;
use Oneduo\NovaFileManager\Events\FileUploaded;
use Oneduo\NovaFileManager\Events\FileUploading;
use Oneduo\NovaFileManager\NovaFileManager;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->disk = 'public';
    Storage::fake($this->disk);
});

it('can upload file to the root folder', function () {
    Event::fake();

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
        event: FileUploading::class,
        callback: function (FileUploading $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertDispatched(
        event: FileUploaded::class,
        callback: function (FileUploaded $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

it('can upload file to a nested folder', function () {
    Event::fake();

    Storage::disk($this->disk)->makeDirectory($folder = 'test');

    postJson(
        uri: route('nova-file-manager.files.upload'),
        data: [
            'disk' => $this->disk,
            'path' => $folder,
            'file' => UploadedFile::fake()->image($file = 'image.jpeg'),
            'resumableFilename' => $file,
        ],
    )
        ->assertOk()
        ->assertJson([
            'message' => __('nova-file-manager::messages.file.upload'),
        ]);

    Storage::disk($this->disk)->assertExists($path = "{$folder}/{$file}");

    Event::assertDispatched(
        event: FileUploading::class,
        callback: function (FileUploading $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertDispatched(
        event: FileUploaded::class,
        callback: function (FileUploaded $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

it('can prevent folder creation on upload', function () {
    Nova::$tools = [
        NovaFileManager::make()
            ->canCreateFolder(fn () => false),
    ];

    postJson(
        uri: route('nova-file-manager.files.upload'),
        data: [
            'disk' => $this->disk,
            'path' => '/',
            'file' => UploadedFile::fake()->image($file = 'image.jpeg'),
            'resumableFilename' => $path = "upload/{$file}",
        ],
    )
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'file' => [
                __('nova-file-manager::errors.authorization.unauthorized', ['action' => 'create folder']),
            ],
        ]);
});

it('cannot upload a file with an existing file name when the upload_replace_existing is false', function () {
    Event::fake();

    Nova::$tools = [
        NovaFileManager::make(),
    ];

    config()->set('nova-file-manager.upload_replace_existing', false);

    Storage::disk($this->disk)->put($first = 'first.txt', Str::random());

    postJson(
        uri: route('nova-file-manager.files.upload'),
        data: [
            'disk' => $this->disk,
            'path' => '/',
            'file' => UploadedFile::fake()->create($first),
            'resumableFilename' => $first,
        ],
    )
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'file' => [
                __('nova-file-manager::validation.path.exists', ['path' => "/{$first}"]),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileUploaded::class,
        callback: function (FileUploaded $event) use ($first) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === "/{$first}";
        },
    );
});

it('cannot upload a file with an existing file name when the upload_replace_existing is programmatically false', function () {
    Event::fake();

    Nova::$tools = [
        NovaFileManager::make()
            ->uploadReplaceExisting(fn() => false),
    ];

    config()->set('nova-file-manager.upload_replace_existing', true);

    Storage::disk($this->disk)->put($first = 'first.txt', Str::random());

    postJson(
        uri: route('nova-file-manager.files.upload'),
        data: [
            'disk' => $this->disk,
            'path' => '/',
            'file' => UploadedFile::fake()->create($first),
            'resumableFilename' => $first,
        ],
    )
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'file' => [
                __('nova-file-manager::validation.path.exists', ['path' => "/{$first}"]),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileUploaded::class,
        callback: function (FileUploaded $event) use ($first) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === "/{$first}";
        },
    );
});

it('can upload a file with an existing file name when the upload_replace_existing is true', function () {
    Event::fake();

    Nova::$tools = [
        NovaFileManager::make(),
    ];

    config()->set('nova-file-manager.upload_replace_existing', true);

    Storage::disk($this->disk)->put($first = 'first.txt', "first");

    postJson(
        uri: route('nova-file-manager.files.upload'),
        data: [
            'disk' => $this->disk,
            'path' => '/',
            'file' => UploadedFile::fake()->createWithContent($first, "second"),
            'resumableFilename' => $first,
        ],
    )
        ->assertOk()
        ->assertJson([
            'message' => __('nova-file-manager::messages.file.upload'),
        ]);

    Storage::disk($this->disk)->assertExists($first);

    Event::assertDispatched(
        event: FileUploading::class,
        callback: function (FileUploading $event) use ($first) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $first;
        },
    );

    Event::assertDispatched(
        event: FileUploaded::class,
        callback: function (FileUploaded $event) use ($first) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $first;
        },
    );

    expect(Storage::disk($this->disk)->get($first))
        ->toBe('second');
});

it('can upload a file with an existing file name when the upload_replace_existing is programmatically true', function () {
    Event::fake();

    Nova::$tools = [
        NovaFileManager::make()
            ->uploadReplaceExisting(fn() => true),
    ];

    config()->set('nova-file-manager.upload_replace_existing', false);

    Storage::disk($this->disk)->put($first = 'first.txt', "first");

    postJson(
        uri: route('nova-file-manager.files.upload'),
        data: [
            'disk' => $this->disk,
            'path' => '/',
            'file' => UploadedFile::fake()->createWithContent($first, "second"),
            'resumableFilename' => $first,
        ],
    )
        ->assertOk()
        ->assertJson([
            'message' => __('nova-file-manager::messages.file.upload'),
        ]);

    Storage::disk($this->disk)->assertExists($first);

    Event::assertDispatched(
        event: FileUploading::class,
        callback: function (FileUploading $event) use ($first) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $first;
        },
    );

    Event::assertDispatched(
        event: FileUploaded::class,
        callback: function (FileUploaded $event) use ($first) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $first;
        },
    );

    expect(Storage::disk($this->disk)->get($first))
        ->toBe('second');
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
        event: FileRenaming::class,
        callback: function (FileRenaming $event) use ($path, $new) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $path && $event->to === $new;
        },
    );

    Event::assertDispatched(
        event: FileRenamed::class,
        callback: function (FileRenamed $event) use ($path, $new) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $path && $event->to === $new;
        },
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
        event: FileRenaming::class,
        callback: function (FileRenaming $event) use ($path, $new) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $path && $event->to === $new;
        },
    );

    Event::assertNotDispatched(
        event: FileRenamed::class,
        callback: function (FileRenamed $event) use ($path, $new) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $path && $event->to === $new;
        },
    );
});

it('cannot rename a file to an existing name', function () {
    Event::fake();

    Storage::disk($this->disk)->put($fisrt = 'first.txt', Str::random());
    Storage::disk($this->disk)->put($second = 'second.txt', Str::random());

    Storage::disk($this->disk)->assertExists($fisrt);
    Storage::disk($this->disk)->assertExists($second);

    postJson(
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
        event: FileRenaming::class,
        callback: function (FileRenaming $event) use ($fisrt, $second) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $fisrt && $event->to === $second;
        },
    );

    Event::assertNotDispatched(
        event: FileRenamed::class,
        callback: function (FileRenamed $event) use ($fisrt, $second) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $fisrt && $event->to === $second;
        },
    );
});

it('throws an exception if the filesystem cannot rename the file', function () {
    Event::fake();

    $mock = Mockery::mock(FileManagerContract::class);
    $mock->shouldReceive('rename')->andReturn(false);
    $mock->shouldReceive('filesystem')->andReturn(Storage::disk($this->disk));
    $mock->shouldReceive('getDisk')->andReturn($this->disk);

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

    Event::assertDispatched(
        event: FileRenaming::class,
        callback: function (FileRenaming $event) use ($path, $new) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $path && $event->to === $new;
        },
    );

    Event::assertNotDispatched(
        event: FileRenamed::class,
        callback: function (FileRenamed $event) use ($path, $new) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $path && $event->to === $new;
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
            'paths' => [$path],
        ],
    )
        ->assertOk();

    Storage::disk($this->disk)->assertMissing($path);

    Event::assertDispatched(
        event: FileDeleting::class,
        callback: function (FileDeleting $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk &&
                $event->path === $path;
        },
    );

    Event::assertDispatched(
        event: FileDeleted::class,
        callback: function (FileDeleted $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk &&
                $event->path === $path;
        },
    );
});

it('cant delete a non existing file', function () {
    Event::fake();

    $path = 'file.txt';

    Storage::disk($this->disk)->assertMissing($path);

    postJson(
        uri: route('nova-file-manager.files.delete'),
        data: [
            'disk' => $this->disk,
            'paths' => [$path],
        ],
    )
        ->assertJsonValidationErrors([
            'paths.0' => [
                __('nova-file-manager::validation.path.missing', ['path' => $path]),
            ],
        ]);

    Event::assertNotDispatched(
        event: FileDeleting::class,
        callback: function (FileDeleting $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertNotDispatched(
        event: FileDeleted::class,
        callback: function (FileDeleted $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

it('throws an exception if the filesystem cannot delete the file', function () {
    Event::fake();

    $mock = Mockery::mock(FileManagerContract::class);
    $mock->shouldReceive('delete')->andReturn(false);
    $mock->shouldReceive('filesystem')->andReturn(Storage::disk($this->disk));
    $mock->shouldReceive('getDisk')->andReturn($this->disk);

    app()->instance(FileManagerContract::class, $mock);

    Storage::disk($this->disk)->put($path = 'file.txt', Str::random());

    Storage::disk($this->disk)->assertExists($path);

    postJson(
        uri: route('nova-file-manager.files.delete'),
        data: [
            'disk' => $this->disk,
            'paths' => [$path],
        ],
    )
        ->assertJsonValidationErrors([
            'paths' => [
                __('nova-file-manager::errors.file.delete'),
            ],
        ]);

    Event::assertDispatched(
        event: FileDeleting::class,
        callback: function (FileDeleting $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertNotDispatched(
        event: FileDeleted::class,
        callback: function (FileDeleted $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

it('can unzip an archive', function () {
    Event::fake();

    Storage::disk($this->disk)->put(
        path: $path = 'archive.zip',
        contents: file_get_contents(base_path('../../../../tests/Fixture/storage/oneduo.zip'))
    );

    Storage::disk($this->disk)->assertExists($path);

    postJson(
        uri: route('nova-file-manager.files.unzip'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ],
    )
        ->assertOk()
        ->assertJson([
            'message' => __('nova-file-manager::messages.file.unzip'),
        ]);

    Storage::disk($this->disk)->assertExists('archive/oneduo/confidential/secret.txt');

    Event::assertDispatched(
        event: FileUnzipping::class,
        callback: function (FileUnzipping $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertDispatched(
        event: FileUnzipped::class,
        callback: function (FileUnzipped $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

it('throws an exception if the filesystem cannot unzip the archive', function () {
    Event::fake();

    $mock = Mockery::mock(FileManagerContract::class);
    $mock->shouldReceive('unzip')->andReturn(false);
    $mock->shouldReceive('filesystem')->andReturn(Storage::disk($this->disk));
    $mock->shouldReceive('getDisk')->andReturn($this->disk);

    app()->instance(FileManagerContract::class, $mock);

    Storage::disk($this->disk)->put(
        path: $path = 'archive.zip',
        contents: file_get_contents(base_path('../../../../tests/Fixture/storage/oneduo.zip'))
    );

    Storage::disk($this->disk)->assertExists($path);

    postJson(
        uri: route('nova-file-manager.files.unzip'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ],
    )
        ->assertJsonValidationErrors([
            'path' => [
                __('nova-file-manager::errors.file.unzip'),
            ],
        ]);

    Storage::disk($this->disk)->assertMissing('archive/oneduo/confidential/secret.txt');

    Event::assertDispatched(
        event: FileUnzipping::class,
        callback: function (FileUnzipping $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertNotDispatched(
        event: FileUnzipped::class,
        callback: function (FileUnzipped $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

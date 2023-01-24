<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Oneduo\NovaFileManager\Contracts\Services\FileManagerContract;
use Oneduo\NovaFileManager\Events\FolderCreated;
use Oneduo\NovaFileManager\Events\FolderCreating;
use Oneduo\NovaFileManager\Events\FolderDeleted;
use Oneduo\NovaFileManager\Events\FolderDeleting;
use Oneduo\NovaFileManager\Events\FolderRenamed;
use Oneduo\NovaFileManager\Events\FolderRenaming;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->disk = 'public';
    Storage::fake($this->disk);
});

it('can create a directory', function () {
    Event::fake();

    $path = 'new folder';

    postJson(
        uri: route('nova-file-manager.folders.create'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ],
    )
        ->assertOk();

    Storage::disk($this->disk)->assertExists($path);

    Event::assertDispatched(
        event: FolderCreating::class,
        callback: function (FolderCreating $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertDispatched(
        event: FolderCreated::class,
        callback: function (FolderCreated $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

it('throws an exception if the filesystem cannot create the directory', function () {
    Event::fake();

    $mock = mock(FileManagerContract::class)->expect(
        mkdir: fn ($path) => false,
        filesystem: fn () => Storage::disk($this->disk),
        getDisk: fn () => $this->disk,
    );

    app()->instance(FileManagerContract::class, $mock);

    $path = 'new folder';

    postJson(
        uri: route('nova-file-manager.folders.create'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ],
    )
        ->assertJsonValidationErrors([
            'folder' => [
                __('nova-file-manager::errors.folder.create'),
            ],
        ]);

    Event::assertDispatched(
        event: FolderCreating::class,
        callback: function (FolderCreating $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertNotDispatched(
        event: FolderCreated::class,
        callback: function (FolderDeleted $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

it('cannot create a directory with an existing name', function () {
    Storage::disk($this->disk)->makeDirectory('existing');

    postJson(
        uri: route('nova-file-manager.folders.create'),
        data: [
            'disk' => $this->disk,
            'path' => $path = 'existing',
        ],
    )
        ->assertJsonValidationErrors([
            'path' => [
                __('nova-file-manager::validation.path.exists', ['path' => $path]),
            ],
        ]);
});

it('can rename a directory', function () {
    Event::fake();

    Storage::disk($this->disk)->makeDirectory($old = 'existing');

    $new = 'renamed';

    postJson(
        uri: route('nova-file-manager.folders.rename'),
        data: [
            'disk' => $this->disk,
            'from' => $old,
            'to' => $new,
        ],
    )
        ->assertOk();

    Storage::disk($this->disk)->assertMissing($old);
    Storage::disk($this->disk)->assertExists($new);

    Event::assertDispatched(
        event: FolderRenaming::class,
        callback: function (FolderRenaming $event) use ($old, $new) {
            return $event->filesystem === Storage::disk($this->disk)
            && $event->disk === $this->disk
            && $event->from === $old
            && $event->to === $new;
        },
    );

    Event::assertDispatched(
        event: FolderRenamed::class,
        callback: function (FolderRenamed $event) use ($old, $new) {
            return $event->filesystem === Storage::disk($this->disk)
            && $event->disk === $this->disk
            && $event->from === $old
            && $event->to === $new;
        },
    );
});

it('returns validation error when the filesystem can not rename the directory', function () {
    Event::fake();

    $mock = mock(FileManagerContract::class)->expect(
        rename: fn ($path) => false,
        filesystem: fn () => Storage::disk($this->disk),
        getDisk: fn () => $this->disk,
    );

    app()->instance(FileManagerContract::class, $mock);

    Storage::disk($this->disk)->makeDirectory($old = 'existing');

    $new = 'renamed';

    postJson(
        uri: route('nova-file-manager.folders.rename'),
        data: [
            'disk' => $this->disk,
            'from' => $old,
            'to' => $new,
        ],
    )
        ->assertJsonValidationErrors([
            'folder' => [
                __('nova-file-manager::errors.folder.rename'),
            ],
        ]);

    Event::assertDispatched(
        event: FolderRenaming::class,
        callback: function (FolderRenaming $event) use ($old, $new) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $old
                && $event->to === $new;
        },
    );

    Event::assertNotDispatched(
        event: FolderRenamed::class,
        callback: function (FolderRenamed $event) use ($old, $new) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->from === $old
                && $event->to === $new;
        },
    );
});

it('cannot rename a directory which doesnt exist', function () {
    $response = postJson(
        uri: route('nova-file-manager.folders.rename'),
        data: [
            'disk' => $this->disk,
            'from' => $path = 'existing',
            'to' => 'renamed',
        ],
    );

    $response
        ->assertJsonValidationErrors([
            'from' => [
                __('nova-file-manager::validation.path.missing', ['path' => $path]),
            ],
        ]);
});

it('cannot rename a directory to an existing name', function () {
    Storage::disk($this->disk)->makeDirectory($first = 'first');
    Storage::disk($this->disk)->makeDirectory($second = 'second');

    $response = postJson(
        uri: route('nova-file-manager.folders.rename'),
        data: [
            'disk' => $this->disk,
            'from' => $first,
            'to' => $second,
        ],
    );

    $response
        ->assertJsonValidationErrors([
            'to' => [
                __('nova-file-manager::validation.path.exists', ['path' => $second]),
            ],
        ]);
});

it('can delete a directory', function () {
    Event::fake();

    Storage::disk($this->disk)->makeDirectory($path = 'directory');
    Storage::disk($this->disk)->assertExists($path);

    postJson(
        uri: route('nova-file-manager.folders.delete'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ]
    )
        ->assertOk();

    Storage::disk($this->disk)->assertMissing($path);

    Event::assertDispatched(
        event: FolderDeleting::class,
        callback: function (FolderDeleting $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertDispatched(
        event: FolderDeleted::class,
        callback: function (FolderDeleted $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

it('cannot delete a directory which doesnt exist', function () {
    Event::fake();

    $path = 'directory';

    Storage::disk($this->disk)->assertMissing($path);

    postJson(
        uri: route('nova-file-manager.folders.delete'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ]
    )
        ->assertJsonValidationErrors([
            'path' => [
                __('nova-file-manager::validation.path.missing', ['path' => $path]),
            ],
        ]);

    Event::assertNotDispatched(
        event: FolderDeleting::class,
        callback: function (FolderDeleting $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertNotDispatched(
        event: FolderDeleted::class,
        callback: function (FolderDeleted $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

it('throws an exception if the filesystem cannot delete the directory', function () {
    Event::fake();

    $mock = mock(FileManagerContract::class)->expect(
        rmdir: fn ($path) => false,
        filesystem: fn () => Storage::disk($this->disk),
        getDisk: fn () => $this->disk,
    );

    app()->instance(FileManagerContract::class, $mock);

    Storage::disk($this->disk)->makeDirectory($path = 'directory');

    expect(Storage::disk($this->disk)->exists($path))->toBeTrue();

    postJson(
        uri: route('nova-file-manager.folders.delete'),
        data: [
            'disk' => $this->disk,
            'path' => $path,
        ]
    )
        ->assertJsonValidationErrors([
            'folder' => [
                __('nova-file-manager::errors.folder.delete'),
            ],
        ]);

    Event::assertDispatched(
        event: FolderDeleting::class,
        callback: function (FolderDeleting $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );

    Event::assertNotDispatched(
        event: FolderDeleted::class,
        callback: function (FolderDeleted $event) use ($path) {
            return $event->filesystem === Storage::disk($this->disk)
                && $event->disk === $this->disk
                && $event->path === $path;
        },
    );
});

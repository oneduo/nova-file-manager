<?php

declare(strict_types=1);

use BBSLab\NovaFileManager\Contracts\Services\FileManagerContract;
use BBSLab\NovaFileManager\Events\FolderCreated;
use BBSLab\NovaFileManager\Events\FolderDeleted;
use BBSLab\NovaFileManager\Events\FolderRenamed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
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

    expect(Storage::disk($this->disk)->exists($path))->toBeTrue();

    Event::assertDispatched(
        event: FolderCreated::class,
        callback: fn (FolderCreated $event) => $event->disk === $this->disk && $event->path === $path,
    );
});

it('cannot create a directory with an existing name', function () {
    Storage::disk($this->disk)->makeDirectory('existing');

    postJson(
        uri: route('nova-file-manager.folders.create'),
        data: [
            'disk' => $this->disk,
            'path' => 'existing',
        ],
    )
        ->assertJsonValidationErrors([
            'folder' => [
                __('Folder already exists !'),
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
            'oldPath' => $old,
            'newPath' => $new,
        ],
    )
        ->assertOk();

    expect(Storage::disk($this->disk)->exists($old))->toBeFalse();
    expect(Storage::disk($this->disk)->exists($new))->toBeTrue();

    Event::assertDispatched(
        event: FolderRenamed::class,
        callback: fn (FolderRenamed $event) => $event->disk === $this->disk
            && $event->oldPath === $old
            && $event->newPath === $new,
    );
});

it('returns validation error when the filesystem can not rename the directory', function () {
    Event::fake();

    $mock = mock(FileManagerContract::class)->expect(
        rename: fn ($path) => false,
    );

    app()->instance(FileManagerContract::class, $mock);

    Storage::disk($this->disk)->makeDirectory($old = 'existing');

    $new = 'renamed';

    postJson(
        uri: route('nova-file-manager.folders.rename'),
        data: [
            'disk' => $this->disk,
            'oldPath' => $old,
            'newPath' => $new,
        ],
    )
        ->assertJsonValidationErrors([
            'folder' => [
                __('Could not rename folder !'),
            ],
        ]);

    Event::assertNotDispatched(
        event: FolderRenamed::class,
        callback: fn (FolderRenamed $event) => $event->disk === $this->disk
            && $event->oldPath === $old
            && $event->newPath === $new,
    );
});

it('cannot rename a directory which doesnt exist', function () {
    $response = postJson(
        uri: route('nova-file-manager.folders.rename'),
        data: [
            'disk' => $this->disk,
            'oldPath' => 'existing',
            'newPath' => 'renamed',
        ],
    );

    $response
        ->assertJsonValidationErrors([
            'oldPath' => [
                __('validation.exists', ['attribute' => 'old path']),
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
            'oldPath' => $first,
            'newPath' => $second,
        ],
    );

    $response
        ->assertJsonValidationErrors([
            'newPath' => [
                __('validation.exists', ['attribute' => 'new path']),
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
        event: FolderDeleted::class,
        callback: fn (FolderDeleted $event) => $event->disk === $this->disk && $event->path === $path,
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
                __('validation.exists', ['attribute' => 'path']),
            ],
        ]);

    Event::assertNotDispatched(
        event: FolderDeleted::class,
        callback: fn (FolderDeleted $event) => $event->disk === $this->disk && $event->path === $path,
    );
});

it('throws an exception if the filesystem cannot delete the directory', function () {
    Event::fake();

    $mock = mock(FileManagerContract::class)->expect(
        rmdir: fn ($path) => false,
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
                __('Could not delete folder !'),
            ],
        ]);

    Event::assertNotDispatched(
        event: FolderDeleted::class,
        callback: fn (FolderDeleted $event) => $event->disk === $this->disk && $event->path === $path,
    );
});

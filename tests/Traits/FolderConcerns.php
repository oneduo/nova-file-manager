<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Tests\Traits;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;

trait FolderConcerns
{
    protected ?string $disk = null;

    protected ?User $user = null;

    public function performAuthorizedCreateChecks(): void
    {
        test()->tap(function () {
            $path = 'new folder';

            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.folders.create'),
                    data: [
                        'disk' => $this->disk,
                        'path' => $path,
                    ],
                )
                ->assertOk();

            Storage::disk($this->disk)->assertExists($path);
        });
    }

    public function performUnauthorizedCreateChecks(?string $message = null): void
    {
        test()->tap(function () use ($message) {
            $path = 'new folder';

            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.folders.create'),
                    data: [
                        'disk' => $this->disk,
                        'path' => $path,
                    ],
                )
                ->assertUnprocessable()
                ->assertJsonValidationErrors([
                    'folder' => [
                        $message ?? __('This action is unauthorized.'),
                    ],
                ]);

            Storage::disk($this->disk)->assertMissing($path);
        });
    }

    public function performAuthorizedRenameChecks(): void
    {
        test()->tap(function () {
            Storage::disk($this->disk)->makeDirectory($old = 'existing');

            $new = 'renamed';

            actingAs($this->user)
                ->postJson(
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
        });
    }

    public function performUnauthorizedRenameChecks(?string $message = null): void
    {
        test()->tap(function () use ($message) {
            Storage::disk($this->disk)->makeDirectory($old = 'existing');

            $new = 'renamed';

            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.folders.rename'),
                    data: [
                        'disk' => $this->disk,
                        'from' => $old,
                        'to' => $new,
                    ],
                )
                ->assertUnprocessable()
                ->assertJsonValidationErrors([
                    'folder' => [
                        $message ?? __('This action is unauthorized.'),
                    ],
                ]);

            Storage::disk($this->disk)->assertExists($old);
            Storage::disk($this->disk)->assertMissing($new);
        });
    }

    public function performAuthorizedDeleteChecks(): void
    {
        test()->tap(function () {
            Storage::disk($this->disk)->makeDirectory($path = 'directory');
            Storage::disk($this->disk)->assertExists($path);

            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.folders.delete'),
                    data: [
                        'disk' => $this->disk,
                        'path' => $path,
                    ]
                )
                ->assertOk();

            Storage::disk($this->disk)->assertMissing($path);
        });
    }

    public function performUnauthorizedDeleteChecks(?string $message = null): void
    {
        test()->tap(function () use ($message) {
            Storage::disk($this->disk)->makeDirectory($path = 'directory');
            Storage::disk($this->disk)->assertExists($path);

            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.folders.delete'),
                    data: [
                        'disk' => $this->disk,
                        'path' => $path,
                    ]
                )
                ->assertUnprocessable()
                ->assertJsonValidationErrors([
                    'folder' => [
                        $message ?? __('This action is unauthorized.'),
                    ],
                ]);

            Storage::disk($this->disk)->assertExists($path);
        });
    }
}

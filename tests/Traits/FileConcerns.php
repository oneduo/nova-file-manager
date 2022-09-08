<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Tests\Traits;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Pest\Laravel\actingAs;

trait FileConcerns
{
    protected ?string $disk = null;

    protected ?User $user = null;

    public function performAuthorizedUploadChecks(): void
    {
        test()->tap(function () {
            actingAs($this->user)
                ->postJson(
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
        });
    }

    public function performUnauthorizedUploadChecks(?string $message = null): void
    {
        test()->tap(function () use ($message) {
            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.files.upload'),
                    data: [
                        'disk' => $this->disk,
                        'path' => '/',
                        'file' => UploadedFile::fake()->image($path = 'image.jpeg'),
                    ],
                )
                ->assertUnprocessable()
                ->assertJsonValidationErrors([
                    'file' => [$message ?? __('This action is unauthorized.')],
                ]);

            Storage::disk($this->disk)->assertMissing($path);
        });
    }

    public function performAuthorizedRenameChecks(): void
    {
        test()->tap(function () {
            Storage::disk($this->disk)->put($old = 'file.txt', Str::random());

            $new = 'renamed';

            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.files.rename'),
                    data: [
                        'disk' => $this->disk,
                        'oldPath' => $old,
                        'newPath' => $new,
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
            Storage::disk($this->disk)->put($old = 'file.txt', Str::random());

            $new = 'renamed';

            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.files.rename'),
                    data: [
                        'disk' => $this->disk,
                        'oldPath' => $old,
                        'newPath' => $new,
                    ],
                )
                ->assertUnprocessable()
                ->assertJsonValidationErrors([
                    'file' => [
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
            Storage::disk($this->disk)->put($path = 'file.txt', Str::random());

            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.files.delete'),
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
            Storage::disk($this->disk)->put($path = 'file.txt', Str::random());

            actingAs($this->user)
                ->postJson(
                    uri: route('nova-file-manager.files.delete'),
                    data: [
                        'disk' => $this->disk,
                        'path' => $path,
                    ]
                )
                ->assertUnprocessable()
                ->assertJsonValidationErrors([
                    'file' => [
                            $message ?? __('This action is unauthorized.'),
                    ],
                ]);

            Storage::disk($this->disk)->assertExists($path);
        });
    }
}

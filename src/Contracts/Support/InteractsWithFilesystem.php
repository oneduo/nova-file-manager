<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Contracts\Support;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property ?\Closure $filesystem
 */
interface InteractsWithFilesystem extends ResolvesUrl
{
    public function filesystem(Closure $callback): static;

    public function hasCustomFilesystem(): bool;

    public function resolveFilesystem(NovaRequest $request): Filesystem|string|null;

    public function showCreateFolder(Closure $callback): static;

    public function shouldShowCreateFolder(NovaRequest $request): bool;

    public function showRenameFolder(Closure $callback): static;

    public function shouldShowRenameFolder(NovaRequest $request): bool;

    public function showDeleteFolder(Closure $callback): static;

    public function shouldShowDeleteFolder(NovaRequest $request): bool;

    public function showUploadFile(Closure $callback): static;

    public function shouldShowUploadFile(NovaRequest $request): bool;

    public function showRenameFile(Closure $callback): static;

    public function shouldShowRenameFile(NovaRequest $request): bool;

    public function showDeleteFile(Closure $callback): static;

    public function showUnzipFile(Closure $callback): static;

    public function shouldShowUnzipFile(NovaRequest $request): bool;

    public function shouldShowDeleteFile(NovaRequest $request): bool;

    public function showCropImage(Closure $callback): static;

    public function shouldShowCropImage(NovaRequest $request): bool;

    public function canCreateFolder(Closure $callback): static;

    public function resolveCanCreateFolder(NovaRequest $request): bool;

    public function canRenameFolder(Closure $callback): static;

    public function resolveCanRenameFolder(NovaRequest $request): bool;

    public function canDeleteFolder(Closure $callback): static;

    public function resolveCanDeleteFolder(NovaRequest $request): bool;

    public function canUploadFile(Closure $callback): static;

    public function resolveCanUploadFile(NovaRequest $request): bool;

    public function uploadReplaceExisting(Closure $callback): static;

    public function resolveUploadReplaceExisting(NovaRequest $request): bool;

    public function canRenameFile(Closure $callback): static;

    public function resolveCanRenameFile(NovaRequest $request): bool;

    public function canDeleteFile(Closure $callback): static;

    public function resolveCanDeleteFile(NovaRequest $request): bool;

    public function canUnzipFile(Closure $callback): static;

    public function resolveCanUnzipFile(NovaRequest $request): bool;

    public function hasUploadValidator(): bool;

    public function getUploadValidator(): ?Closure;

    /**
     * Set the validation rules for the upload.
     *
     * @param  callable|array<int, string|\Illuminate\Validation\Rule|\Illuminate\Contracts\Validation\Rule|callable>|string  ...$rules
     * @return $this
     */
    public function uploadRules($rules): static;

    public function getUploadRules(): array;

    public function validateUploadUsing(Closure $callback): static;

    public function pinturaOptions(array $options): static;

    public function cropperOptions(array $options): static;

    public function options(): array;

    public function merge(self $other): static;
}

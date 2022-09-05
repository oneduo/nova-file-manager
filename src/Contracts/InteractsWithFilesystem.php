<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Contracts;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property ?\Closure $filesystemCallback
 */
interface InteractsWithFilesystem
{
    public function filesystem(Closure $callback): static;

    public function hasCustomFilesystem(): bool;

    public function resolveFilesystem(NovaRequest $request): ?Filesystem;

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

    public function shouldShowDeleteFile(NovaRequest $request): bool;

    public function canCreateFolder(Closure $callback): static;

    public function resolveCanCreateFolder(NovaRequest $request): bool;

    public function canRenameFolder(Closure $callback): static;

    public function resolveCanRenameFolder(NovaRequest $request): bool;

    public function canDeleteFolder(Closure $callback): static;

    public function resolveCanDeleteFolder(NovaRequest $request): bool;

    public function canUploadFile(Closure $callback): static;

    public function resolveCanUploadFile(NovaRequest $request): bool;

    public function canRenameFile(Closure $callback): static;

    public function resolveCanRenameFile(NovaRequest $request): bool;

    public function canDeleteFile(Closure $callback): static;

    public function resolveCanDeleteFile(NovaRequest $request): bool;

    public function options(): array;
}

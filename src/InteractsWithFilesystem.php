<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Laravel\Nova\Http\Requests\NovaRequest;

trait InteractsWithFilesystem
{
    protected ?Closure $filesystemCallback = null;

    protected ?Closure $showCreateFolder = null;

    protected ?Closure $showRenameFolder = null;

    protected ?Closure $showDeleteFolder = null;

    protected ?Closure $showUploadFile = null;

    protected ?Closure $showRenameFile = null;

    protected ?Closure $showDeleteFile = null;

    protected ?Closure $showCropImage = null;

    protected ?Closure $canCreateFolder = null;

    protected ?Closure $canRenameFolder = null;

    protected ?Closure $canDeleteFolder = null;

    protected ?Closure $canUploadFile = null;

    protected ?Closure $canRenameFile = null;

    protected ?Closure $canDeleteFile = null;

    public function filesystem(Closure $callback): static
    {
        $this->filesystemCallback = $callback;

        return $this;
    }

    public function hasCustomFilesystem(): bool
    {
        return $this->filesystemCallback !== null && is_callable($this->filesystemCallback);
    }

    public function resolveFilesystem(NovaRequest $request): ?Filesystem
    {
        return $this->hasCustomFilesystem()
            ? call_user_func($this->filesystemCallback, $request)
            : null;
    }

    public function showCreateFolder(Closure $callback): static
    {
        $this->showCreateFolder = $callback;

        return $this;
    }

    public function shouldShowCreateFolder(NovaRequest $request): bool
    {
        return is_callable($this->showCreateFolder)
            ? call_user_func($this->showCreateFolder, $request)
            : true;
    }

    public function showRenameFolder(Closure $callback): static
    {
        $this->showRenameFolder = $callback;

        return $this;
    }

    public function shouldShowRenameFolder(NovaRequest $request): bool
    {
        return is_callable($this->showRenameFolder)
            ? call_user_func($this->showRenameFolder, $request)
            : true;
    }

    public function showDeleteFolder(Closure $callback): static
    {
        $this->showDeleteFolder = $callback;

        return $this;
    }

    public function shouldShowDeleteFolder(NovaRequest $request): bool
    {
        return is_callable($this->showDeleteFolder)
            ? call_user_func($this->showDeleteFolder, $request)
            : true;
    }

    public function showUploadFile(Closure $callback): static
    {
        $this->showUploadFile = $callback;

        return $this;
    }

    public function shouldShowUploadFile(NovaRequest $request): bool
    {
        return is_callable($this->showUploadFile)
            ? call_user_func($this->showUploadFile, $request)
            : true;
    }

    public function showRenameFile(Closure $callback): static
    {
        $this->showRenameFile = $callback;

        return $this;
    }

    public function shouldShowRenameFile(NovaRequest $request): bool
    {
        return is_callable($this->showRenameFile)
            ? call_user_func($this->showRenameFile, $request)
            : true;
    }

    public function showDeleteFile(Closure $callback): static
    {
        $this->showDeleteFile = $callback;

        return $this;
    }

    public function shouldShowDeleteFile(NovaRequest $request): bool
    {
        return is_callable($this->showDeleteFile)
            ? call_user_func($this->showDeleteFile, $request)
            : true;
    }

    public function showCropImage(Closure $callback): static
    {
        $this->showCropImage = $callback;

        return $this;
    }

    public function shouldShowCropImage(NovaRequest $request): bool
    {
        return is_callable($this->showCropImage)
            ? call_user_func($this->showCropImage, $request)
            : true;
    }

    public function canCreateFolder(Closure $callback): static
    {
        $this->canCreateFolder = $callback;

        return $this;
    }

    public function resolveCanCreateFolder(NovaRequest $request): bool
    {
        return is_callable($this->canCreateFolder)
            ? call_user_func($this->canCreateFolder, $request)
            : $this->shouldShowCreateFolder($request);
    }

    public function canRenameFolder(Closure $callback): static
    {
        $this->canRenameFolder = $callback;

        return $this;
    }

    public function resolveCanRenameFolder(NovaRequest $request): bool
    {
        return is_callable($this->canRenameFolder)
            ? call_user_func($this->canRenameFolder, $request)
            : $this->shouldShowRenameFolder($request);
    }

    public function canDeleteFolder(Closure $callback): static
    {
        $this->canDeleteFolder = $callback;

        return $this;
    }

    public function resolveCanDeleteFolder(NovaRequest $request): bool
    {
        return is_callable($this->canDeleteFolder)
            ? call_user_func($this->canDeleteFolder, $request)
            : $this->shouldShowDeleteFolder($request);
    }

    public function canUploadFile(Closure $callback): static
    {
        $this->canUploadFile = $callback;

        return $this;
    }

    public function resolveCanUploadFile(NovaRequest $request): bool
    {
        return is_callable($this->canUploadFile)
            ? call_user_func($this->canUploadFile, $request)
            : $this->shouldShowUploadFile($request);
    }

    public function canRenameFile(Closure $callback): static
    {
        $this->canRenameFile = $callback;

        return $this;
    }

    public function resolveCanRenameFile(NovaRequest $request): bool
    {
        return is_callable($this->canRenameFile)
            ? call_user_func($this->canRenameFile, $request)
            : $this->shouldShowRenameFile($request);
    }

    public function canDeleteFile(Closure $callback): static
    {
        $this->canDeleteFile = $callback;

        return $this;
    }

    public function resolveCanDeleteFile(NovaRequest $request): bool
    {
        return is_callable($this->canDeleteFile)
            ? call_user_func($this->canDeleteFile, $request)
            : $this->shouldShowDeleteFile($request);
    }

    public function options(): array
    {
        return with(app(NovaRequest::class), function (NovaRequest $request) {
            return [
                'customDisk' => $this->hasCustomFilesystem(),
                'permissions' => [
                    'showCreateFolder' => $this->shouldShowCreateFolder($request),
                    'showRenameFolder' => $this->shouldShowRenameFolder($request),
                    'showDeleteFolder' => $this->shouldShowDeleteFolder($request),
                    'showUploadFile' => $this->shouldShowUploadFile($request),
                    'showRenameFile' => $this->shouldShowRenameFile($request),
                    'showDeleteFile' => $this->shouldShowDeleteFile($request),
                    'showCropImage' => $this->shouldShowCropImage($request),
                ],
            ];
        });
    }
}

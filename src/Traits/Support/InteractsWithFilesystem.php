<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Traits\Support;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Validation\Rule;
use Laravel\Nova\Http\Requests\NovaRequest;

trait InteractsWithFilesystem
{
    use ResolvesUrl;

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

    protected array $uploadRules = [];

    protected ?Closure $uploadValidator = null;

    public function filesystem(Closure $callback): static
    {
        $this->filesystemCallback = $callback;

        return $this;
    }

    public function hasCustomFilesystem(): bool
    {
        return $this->filesystemCallback !== null && is_callable($this->filesystemCallback);
    }

    public function resolveFilesystem(NovaRequest $request): Filesystem|string|null
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

    public function hasUploadValidator(): bool
    {
        return $this->uploadValidator !== null && is_callable($this->uploadValidator);
    }

    public function getUploadValidator(): ?Closure
    {
        return $this->uploadValidator;
    }

    /**
     * Set the validation rules for the upload.
     *
     * @param  callable|array<int, string|\Illuminate\Validation\Rule|\Illuminate\Contracts\Validation\Rule|callable>|string  ...$rules
     * @return $this
     */
    public function uploadRules($rules): static
    {
        if ($rules instanceof Closure) {
            $this->uploadRules = [$rules];
        } else {
            $this->uploadRules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;
        }

        return $this;
    }

    public function getUploadRules(): array
    {
        return $this->uploadRules;
    }

    public function validateUploadUsing(Closure $callback): static
    {
        $this->uploadValidator = $callback;

        return $this;
    }

    public function options(): array
    {
        return with(app(NovaRequest::class), function (NovaRequest $request) {
            return [
                'singleDisk' => $this->hasCustomFilesystem(),
                'permissions' => [
                    'folder' => [
                        'create' => $this->shouldShowCreateFolder($request),
                        'rename' => $this->shouldShowRenameFolder($request),
                        'delete' => $this->shouldShowDeleteFolder($request),
                    ],
                    'file' => [
                        'upload' => $this->shouldShowUploadFile($request),
                        'rename' => $this->shouldShowRenameFile($request),
                        'edit' => $this->shouldShowCropImage($request),
                        'delete' => $this->shouldShowDeleteFile($request),
                    ],
                ],
            ];
        });
    }
}

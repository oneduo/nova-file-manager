<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Traits\Support;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Validation\Rule;
use Laravel\Nova\Http\Requests\NovaRequest;
use Oneduo\NovaFileManager\Contracts\Support\InteractsWithFilesystem as InteractsWithFilesystemContract;

trait InteractsWithFilesystem
{
    use ResolvesUrl;

    public ?Closure $filesystem = null;

    public ?Closure $canCreateFolder = null;

    public ?Closure $canRenameFolder = null;

    public ?Closure $canDeleteFolder = null;

    public ?Closure $canUploadFile = null;

    public ?Closure $uploadReplaceExisting = null;

    public ?Closure $canRenameFile = null;

    public ?Closure $canDeleteFile = null;

    public ?Closure $canUnzipFile = null;

    public ?Closure $canDownloadFile = null;

    public ?Closure $showCreateFolder = null;

    public ?Closure $showRenameFolder = null;

    public ?Closure $showDeleteFolder = null;

    public ?Closure $showUploadFile = null;

    public ?Closure $showRenameFile = null;

    public ?Closure $showDeleteFile = null;

    public ?Closure $showUnzipFile = null;

    public ?Closure $showDownloadFile = null;

    public ?Closure $showCropImage = null;

    public array $uploadRules = [];

    public ?Closure $uploadValidator = null;

    public array $pinturaOptions = [];

    public array $cropperOptions = [];

    public function filesystem(Closure $callback): static
    {
        $this->filesystem = $callback;

        return $this;
    }

    public function hasCustomFilesystem(): bool
    {
        return $this->filesystem !== null && is_callable($this->filesystem);
    }

    public function resolveFilesystem(NovaRequest $request): Filesystem|string|null
    {
        return $this->hasCustomFilesystem()
            ? call_user_func($this->filesystem, $request)
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

    public function showUnzipFile(Closure $callback): static
    {
        $this->showUnzipFile = $callback;

        return $this;
    }

    public function shouldShowUnzipFile(NovaRequest $request): bool
    {
        return is_callable($this->showUnzipFile)
            ? call_user_func($this->showUnzipFile, $request)
            : true;
    }

    public function showDownloadFile(Closure $callback): static
    {
        $this->showDownloadFile = $callback;

        return $this;
    }

    public function shouldShowDownloadFile(NovaRequest $request): bool
    {
        return is_callable($this->showDownloadFile)
            ? call_user_func($this->showDownloadFile, $request)
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

    public function uploadReplaceExisting(Closure $callback): static
    {
        $this->uploadReplaceExisting = $callback;

        return $this;
    }

    public function resolveUploadReplaceExisting(NovaRequest $request): bool
    {
        return is_callable($this->uploadReplaceExisting)
            ? call_user_func($this->uploadReplaceExisting, $request)
            : config('nova-file-manager.upload_replace_existing', false);
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

    public function canUnzipFile(Closure $callback): static
    {
        $this->canUnzipFile = $callback;

        return $this;
    }

    public function resolveCanUnzipFile(NovaRequest $request): bool
    {
        return is_callable($this->canUnzipFile)
            ? call_user_func($this->canUnzipFile, $request)
            : $this->shouldShowUnzipFile($request);
    }

    public function canDownloadFile(Closure $callback): static
    {
        $this->canDownloadFile = $callback;

        return $this;
    }

    public function resolveCanDownloadFile(NovaRequest $request): bool
    {
        return is_callable($this->canDownloadFile)
            ? call_user_func($this->canDownloadFile, $request)
            : $this->shouldShowDownloadFile($request);
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

    public function pinturaOptions(array $options): static
    {
        $this->pinturaOptions = array_merge($this->pinturaOptions, $options);

        return $this;
    }

    public function cropperOptions(array $options): static
    {
        $this->cropperOptions = array_merge($this->cropperOptions, $options);

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
                        'delete' => $this->shouldShowDeleteFolder($request),
                        'rename' => $this->shouldShowRenameFolder($request),
                    ],
                    'file' => [
                        'delete' => $this->shouldShowDeleteFile($request),
                        'download' => $this->shouldShowDownloadFile($request),
                        'edit' => $this->shouldShowCropImage($request),
                        'rename' => $this->shouldShowRenameFile($request),
                        'unzip' => $this->shouldShowUnzipFile($request),
                        'upload' => $this->shouldShowUploadFile($request),
                    ],
                ],
                'usePintura' => config('nova-file-manager.use_pintura'),
                'pinturaOptions' => $this->pinturaOptions,
                'cropperOptions' => $this->cropperOptions,
            ];
        });
    }

    public function merge(InteractsWithFilesystemContract $other): static
    {
        if ($other->hasUrlResolver()) {
            $this->resolveUrlUsing($other->getUrlResolver());
        }

        $map = [
            'canCreateFolder',
            'canDeleteFile',
            'canDeleteFolder',
            'canRenameFile',
            'canRenameFolder',
            'canUnzipFile',
            'canUploadFile',
            'cropperOptions',
            'filesystem',
            'pinturaOptions',
            'showCreateFolder',
            'showCropImage',
            'showDeleteFile',
            'showDeleteFolder',
            'showDownloadFile',
            'showRenameFile',
            'showRenameFolder',
            'showUnzipFile',
            'showUploadFile',
            'uploadRules',
        ];

        foreach ($map as $attribute) {
            if ($other->{$attribute}) {
                $this->{$attribute}($other->{$attribute});
            }
        }

        if ($other->uploadValidator) {
            $this->validateUploadUsing($other->uploadValidator);
        }

        return $this;
    }
}

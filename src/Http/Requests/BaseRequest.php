<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Contracts\InteractsWithFilesystem;
use BBSLab\NovaFileManager\Contracts\Services\FileManagerContract;
use BBSLab\NovaFileManager\FileManager;
use BBSLab\NovaFileManager\NovaFileManager;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

/**
 * @property-read ?string $disk
 * @property-read ?string $attribute
 * @property-read ?string $resource
 * @property-read ?string $resourceId
 * @property-read ?bool $fieldMode
 */
class BaseRequest extends NovaRequest
{
    public function manager(): FileManagerContract
    {
        return once(function () {
            $element = $this->element();

            return app(
                abstract: FileManagerContract::class,
                parameters: $element->hasCustomFilesystem() ? ['disk' => $element->resolveFilesystem($this)] : [],
            );
        });
    }

    public function element(): ?InteractsWithFilesystem
    {
        return $this->fieldMode ? $this->resolveField() : $this->resolveTool();
    }

    public function resolveField(): ?InteractsWithFilesystem
    {
        $resource = $this->resourceId ? $this->findResourceOrFail() : $this->newResource();

        return $resource->availableFields($this)
            ->whereInstanceOf(FileManager::class)
            ->findFieldByAttribute($this->attribute, function () {
                abort(404);
            });
    }

    public function resolveTool(): ?InteractsWithFilesystem
    {
        return tap(once(function () {
            return collect(Nova::registeredTools())->first(fn (Tool $tool) => $tool instanceof NovaFileManager);
        }), function (?NovaFileManager $tool) {
            abort_if(is_null($tool), 404);
        });
    }

    public function resource()
    {
        return tap(once(function () {
            return Nova::resourceForKey($this->input('resource'));
        }), function ($resource) {
            abort_if(is_null($resource), 404);
        });
    }

    public function canCreateFolder(): bool
    {
        return $this->element()?->resolveCanCreateFolder($this) ?? true;
    }

    public function canRenameFolder(): bool
    {
        return $this->element()?->resolveCanRenameFolder($this) ?? true;
    }

    public function canDeleteFolder(): bool
    {
        return $this->element()?->resolveCanDeleteFolder($this) ?? true;
    }

    public function canUploadFile(): bool
    {
        return $this->element()?->resolveCanUploadFile($this) ?? true;
    }

    public function canRenameFile(): bool
    {
        return $this->element()?->resolveCanRenameFile($this) ?? true;
    }

    public function canDeleteFile(): bool
    {
        return $this->element()?->resolveCanDeleteFile($this) ?? true;
    }
}

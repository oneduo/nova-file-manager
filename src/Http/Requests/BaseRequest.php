<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Services\FileManagerService;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    protected ?FileManagerService $manager = null;

    public function authorize(): bool
    {
        return true;
    }

    public function manager(
        ?string $disk = null,
        ?string $path = null,
        ?int $page = null,
        ?int $perPage = null,
        ?string $search = null,
    ): FileManagerService {
        if (!$this->manager) {
            $disk ??= $this->input('disk');
            $path ??= $this->input('path', '/');
            $page ??= (int) $this->input('page', 1);
            $perPage ??= (int) $this->input('perPage', 15);
            $search ??= $this->input('search');

            $this->manager = FileManagerService::make($disk, $path, $page, $perPage, $search);
        }

        return $this->manager;
    }
}

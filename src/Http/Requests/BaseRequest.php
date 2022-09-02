<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Contracts\Services\FileManagerContract;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    protected ?FileManagerContract $manager = null;

    public function authorize(): bool
    {
        return true;
    }

    public function manager(): FileManagerContract
    {
        return app(FileManagerContract::class);
    }
}

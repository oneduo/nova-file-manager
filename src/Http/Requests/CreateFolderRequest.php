<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Requests;

use BBSLab\NovaFileManager\Rules\DiskExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateFolderRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'disk' => ['required', 'string', new DiskExistsRule()],
            'path' => ['required', 'string', 'min:1'],
        ];
    }
}

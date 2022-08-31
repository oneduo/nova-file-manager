<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->disk = 'public';
    Storage::fake($this->disk);
});

it('cannot retrieve files of undefined disk', function () {
    getJson(uri: route('nova-file-manager.data').'?'.Arr::query(['disk' => 'unknown']))
        ->assertJsonValidationErrors([
            'disk' => [
                __('validation.exists', ['attribute' => 'disk']),
            ],
        ]);
});

it('can retrieve files', function () {
    Storage::disk($this->disk)->put($path = Str::random().'.txt', Str::random());

    $response = getJson(uri: route('nova-file-manager.data').'?'.Arr::query(['disk' => $this->disk]));

    ray()->clearAll();
    ray($response);

    $response
        ->assertOk()
        ->assertJson([
            'disk' => $this->disk,
            'breadcrumbs' => [],
            'directories' => [],
            'files' => [
                [
                    'path' => $path,
                ],
            ],
            'pagination' => [
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 1,
                'total' => 1,
            ],
        ]);
});

<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Controllers;

use BBSLab\NovaFileManager\NovaFileManager;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class ToolController extends Controller
{
    public function __invoke(NovaRequest $request): Response
    {
        /** @var ?\BBSLab\NovaFileManager\NovaFileManager $tool */
        $tool = collect(Nova::registeredTools())->first(fn (Tool $tool) => $tool instanceof NovaFileManager);

        return Inertia::render('NovaFileManager', [
            'config' => array_merge(
                [
                    'upload' => config('nova-file-manager.upload'),
                ],
                $tool?->options(),
            ),
        ]);
    }
}

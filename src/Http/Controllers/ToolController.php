<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ToolController extends Controller
{
    /**
     * The tool's Inertia component render
     *
     * @return \Inertia\Response
     */
    public function __invoke(): Response
    {
        return Inertia::render('NovaFileManager', [
            'config' => [
                'upload' => config('nova-file-manager.upload'),
            ],
        ]);
    }
}

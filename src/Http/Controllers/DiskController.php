<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DiskController extends Controller
{
    /**
     * Get the available disks
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function available(): JsonResponse
    {
        return response()->json(config('nova-file-manager.available_disks', []));
    }
}

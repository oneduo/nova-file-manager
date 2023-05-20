<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DiskController extends Controller
{
    /**
     * Get the available disks
     */
    public function available(): JsonResponse
    {
        return response()->json(config('nova-file-manager.available_disks', []));
    }
}

<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Oneduo\NovaFileManager\NovaFileManager;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request):mixed  $next
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        $tool = collect(Nova::registeredTools())->first([$this, 'matchesTool']);

        return optional($tool)->authorize($request) ? $next($request) : abort(403);
    }

    /**
     * Determine whether this tool belongs to the package.
     */
    public function matchesTool(Tool $tool): bool
    {
        return $tool instanceof NovaFileManager;
    }
}

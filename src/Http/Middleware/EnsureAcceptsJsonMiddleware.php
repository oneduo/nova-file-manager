<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAcceptsJsonMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}

<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Controllers;

use BBSLab\NovaFileManager\Http\Requests\IndexRequest;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function __invoke(IndexRequest $request)
    {
        $manager = $request->manager();

        $paginator = $manager->paginate(
            $manager->files()
        )->onEachSide(1);

        return response()->json([
            'disk' => $manager->disk,
            'breadcrumbs' => $manager->breadcrumbs(),
            'directories' => $manager->directories(),
            'files' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
        ]);
    }
}

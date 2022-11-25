<?php

declare(strict_types=1);

namespace Oneduo\NovaFileManager\Http\Controllers;

use Illuminate\Routing\Controller;
use Oneduo\NovaFileManager\Http\Requests\IndexRequest;

class IndexController extends Controller
{
    /**
     * Get the data for the tool
     *
     * @param  \Oneduo\NovaFileManager\Http\Requests\IndexRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(IndexRequest $request)
    {
        $manager = $request->manager();

        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
        $paginator = $manager
            ->paginate($manager->files())
            ->onEachSide(1);

        return response()->json([
            'disk' => $manager->getDisk(),
            'breadcrumbs' => $manager->breadcrumbs(),
            'folders' => $manager->directories(),
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

<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Controllers;

use BBSLab\NovaFileManager\Events\FolderCreated;
use BBSLab\NovaFileManager\Events\FolderDeleted;
use BBSLab\NovaFileManager\Events\FolderRenamed;
use BBSLab\NovaFileManager\Http\Requests\CreateFolderRequest;
use BBSLab\NovaFileManager\Http\Requests\DeleteFolderRequest;
use BBSLab\NovaFileManager\Http\Requests\RenameFolderRequest;
use BBSLab\NovaFileManager\Services\FileManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class FolderController extends Controller
{
    public function create(CreateFolderRequest $request): JsonResponse
    {
        $result = $request->manager()->mkdir(
            $path = trim($request->get('path'))
        );

        if (!$result) {
            return response()->json(
                [
                    'errors' => [
                        'folder' => [__('Folder already exists !')],
                    ],
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        event(new FolderCreated($request->manager()->disk, $path));

        return response()->json([
            'message' => __('Folder created successfully.'),
        ]);
    }

    public function rename(RenameFolderRequest $request): JsonResponse
    {
        $disk = $request->get('disk');
        $path = $request->get('path');
        $oldPath = $request->get('oldPath');
        $newPath = $request->get('newPath');

        $result = FileManagerService::make($disk, $path)->rename($oldPath, $newPath);

        if (!$result) {
            return response()->json(
                [
                    'errors' => [
                        'folder' => [__('Could not rename folder !')],
                    ],
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        event(new FolderRenamed($disk, $oldPath, $newPath));

        return response()->json([
            'message' => __('Folder renamed successfully.'),
        ]);
    }

    public function delete(DeleteFolderRequest $request): JsonResponse
    {
        $path = $request->get('path');
        $disk = $request->get('disk');

        $result = FileManagerService::make($disk, $path)->rmdir($path);

        if (!$result) {
            return response()->json(
                [
                    'errors' => [
                        'folder' => [__('Could not delete folder !')],
                    ],
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        event(new FolderDeleted($disk, $path));

        return response()->json([
            'message' => __('Folder deleted successfully.'),
        ]);
    }
}

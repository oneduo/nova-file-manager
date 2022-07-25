<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Controllers;

use BBSLab\NovaFileManager\Contracts\Filesystem\Upload\Uploader;
use BBSLab\NovaFileManager\Events\FileDeleted;
use BBSLab\NovaFileManager\Events\FileRenamed;
use BBSLab\NovaFileManager\Http\Requests\DeleteFileRequest;
use BBSLab\NovaFileManager\Http\Requests\DownloadFileRequest;
use BBSLab\NovaFileManager\Http\Requests\RenameFileRequest;
use BBSLab\NovaFileManager\Http\Requests\UploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    public function upload(UploadRequest $request, Uploader $uploader): JsonResponse
    {
        return response()->json(
            $uploader->handle($request)
        );
    }

    public function rename(RenameFileRequest $request): JsonResponse
    {
        $oldPath = $request->get('oldPath');
        $newPath = $request->get('newPath');

        $manager = $request->manager();

        $result = $manager->rename($oldPath, $newPath);

        if (!$result) {
            return response()->json(
                [
                    'error' => __('Could not rename file !'),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        event(new FileRenamed($manager->disk, $oldPath, $newPath));

        return response()->json([
            'message' => __('File renamed successfully.'),
        ]);
    }

    public function delete(DeleteFileRequest $request): JsonResponse
    {
        $path = $request->get('path');

        $manager = $request->manager();

        $result = $manager->delete($path);

        if (!$result) {
            return response()->json(
                [
                    'error' => __('Could not delete file !'),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        event(new FileDeleted($manager->disk, $path));

        return response()->json([
            'message' => __('File deleted successfully.'),
        ]);
    }

    public function donwload(DownloadFileRequest $request): BinaryFileResponse
    {
        $disk = $request->get('disk');
        $path = $request->get('path');

        return response()->download(Storage::disk($disk)->path($path));
    }
}

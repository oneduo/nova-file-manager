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
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    /**
     * Upload a file from the tool
     *
     * @param  \BBSLab\NovaFileManager\Http\Requests\UploadRequest  $request
     * @param  \BBSLab\NovaFileManager\Contracts\Filesystem\Upload\Uploader  $uploader
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(UploadRequest $request, Uploader $uploader): JsonResponse
    {
        return response()->json(
            $uploader->handle($request)
        );
    }

    /**
     * Rename a file
     *
     * @param  \BBSLab\NovaFileManager\Http\Requests\RenameFileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rename(RenameFileRequest $request): JsonResponse
    {
        $manager = $request->manager();
        $result = $manager->rename($request->oldPath, $request->newPath);

        if (!$result) {
            throw ValidationException::withMessages([
                'oldPath' => [__('Could not rename file !')],
            ]);
        }

        event(new FileRenamed($manager->disk, $request->oldPath, $request->newPath));

        return response()->json([
            'message' => __('File renamed successfully.'),
        ]);
    }

    /**
     * Delete a file
     *
     * @param  \BBSLab\NovaFileManager\Http\Requests\DeleteFileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteFileRequest $request): JsonResponse
    {
        $manager = $request->manager();

        $result = $manager->delete($request->path);

        if (!$result) {
            throw ValidationException::withMessages([
                'path' => [__('Could not delete file !')],
            ]);
        }

        event(new FileDeleted($manager->disk, $request->path));

        return response()->json([
            'message' => __('File deleted successfully.'),
        ]);
    }

    /**
     * Download a file
     *
     * @param  \BBSLab\NovaFileManager\Http\Requests\DownloadFileRequest  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(DownloadFileRequest $request): BinaryFileResponse
    {
        return response()->download(Storage::disk($request->disk)->path($request->path));
    }
}

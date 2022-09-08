<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Controllers;

use BBSLab\NovaFileManager\Contracts\Filesystem\Upload\Uploader;
use BBSLab\NovaFileManager\Events\FileDeleted;
use BBSLab\NovaFileManager\Events\FileRenamed;
use BBSLab\NovaFileManager\Http\Requests\DeleteFileRequest;
use BBSLab\NovaFileManager\Http\Requests\RenameFileRequest;
use BBSLab\NovaFileManager\Http\Requests\UploadFileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class FileController extends Controller
{
    /**
     * Upload a file from the tool
     *
     * @param  \BBSLab\NovaFileManager\Http\Requests\UploadFileRequest  $request
     * @param  \BBSLab\NovaFileManager\Contracts\Filesystem\Upload\Uploader  $uploader
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(UploadFileRequest $request, Uploader $uploader): JsonResponse
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
                'oldPath' => [__('nova-file-manager::errors.file.rename')],
            ]);
        }

        event(new FileRenamed($manager->disk, $request->oldPath, $request->newPath));

        return response()->json([
            'message' => __('nova-file-manager::messages.file.create'),
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
                'path' => [__('nova-file-manager::errors.file.delete')],
            ]);
        }

        event(new FileDeleted($manager->disk, $request->path));

        return response()->json([
            'message' => __('nova-file-manager::messages.file.delete'),
        ]);
    }
}

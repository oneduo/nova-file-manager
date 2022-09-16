<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Http\Controllers;

use BBSLab\NovaFileManager\Contracts\Filesystem\Upload\Uploader;
use BBSLab\NovaFileManager\Events\FileDeleted;
use BBSLab\NovaFileManager\Events\FileRenamed;
use BBSLab\NovaFileManager\Events\FileUnzipped;
use BBSLab\NovaFileManager\Http\Requests\DeleteFileRequest;
use BBSLab\NovaFileManager\Http\Requests\RenameFileRequest;
use BBSLab\NovaFileManager\Http\Requests\UnzipFileRequest;
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

        $result = $manager->rename($request->from, $request->to);

        if (!$result) {
            throw ValidationException::withMessages([
                'from' => [__('nova-file-manager::errors.file.rename')],
            ]);
        }

        event(new FileRenamed($manager->disk, $request->from, $request->to));

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

    /**
     * Unzip an archive
     *
     * @param  \BBSLab\NovaFileManager\Http\Requests\UnzipFileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unzip(UnzipFileRequest $request): JsonResponse
    {
        $manager = $request->manager();

        $result = $manager->unzip($request->path);


        if (!$result) {
            throw ValidationException::withMessages([
                'path' => [__('nova-file-manager::errors.file.unzip')],
            ]);
        }

        event(new FileUnzipped($manager->disk, $request->path));

        return response()->json([
            'message' => __('nova-file-manager::messages.file.unzip'),
        ]);
    }
}

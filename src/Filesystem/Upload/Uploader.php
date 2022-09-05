<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Filesystem\Upload;

use BBSLab\NovaFileManager\Contracts\Filesystem\Upload\Uploader as UploaderContract;
use BBSLab\NovaFileManager\Events\FileUploaded;
use BBSLab\NovaFileManager\Http\Requests\UploadRequest;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class Uploader implements UploaderContract
{
    /**
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException
     */
    public function handle(UploadRequest $request, string $index = 'file'): array
    {
        $receiver = new FileReceiver($index, $request, HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException;
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            return $this->saveFile($request, $save->getFile());
        }

        $handler = $save->handler();

        return [
            'done' => $handler->getPercentageDone(),
            'status' => true,
        ];
    }

    public function saveFile(UploadRequest $request, UploadedFile $file): array
    {
        $path = $request->manager()->filesystem()->putFileAs(
            path: $request->path,
            file: $file,
            name: $file->getClientOriginalName(),
        );

        event(new FileUploaded($request->manager()->disk, $path));

        return [
            'message' => __('Uploaded successfully'),
        ];
    }
}

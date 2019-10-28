<?php

namespace LilleBitte\Messenger;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use LilleBitte\Messenger\UploadedFile;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        return new UploadedFile(
            $stream,
            $stream->getSize(),
            $error,
            $clientFilename,
            $clientMediaType
        );
    }
}

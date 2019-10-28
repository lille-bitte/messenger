<?php

namespace LilleBitte\Messenger\Tests\Integration;

use Http\Psr7Test\UploadedFileIntegrationTest;
use LilleBitte\Messenger\UploadedFile;
use LilleBitte\Messenger\Stream;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class UploadedFileTest extends UploadedFileIntegrationTest
{
    /**
     * {@inheritdoc}
     */
    public function createSubject()
    {
        $stream = new Stream('php://memory', 'rw');
        $stream->write('this is a text.');

        return new UploadedFile($stream, $stream->getSize(), \UPLOAD_ERR_OK);
    }
}

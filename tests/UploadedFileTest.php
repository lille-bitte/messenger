<?php

namespace LilleBitte\Teachdaire\Tests;

use LilleBitte\Teachdaire\UploadedFile;
use LilleBitte\Teachdaire\Stream;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class UploadedFileTest extends TestCase
{
    public function testCanUploadIfFilenameIsAString()
    {
        $file = \tempnam(\sys_get_temp_dir(), "shit");
        $stream = new Stream($file, "wb+");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $len = $stream->write("same shit, different day.");
        $this->assertEquals(25, $len);
        $stream->close();
        $upload = new UploadedFile($file, 25);
        $this->assertInstanceOf(UploadedFileInterface::class, $upload);
        $upload->getStream()->close();
    }

    public function testCanUploadIfFilenameIsAResource()
    {
        $file = \tempnam(\sys_get_temp_dir(), "shit");
        $stream = new Stream($file, "wb+");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $len = $stream->write("same shit, different day.");
        $this->assertEquals(25, $len);
        $stream->close();
        $resource = \fopen($file, "wb+");
        $upload = new UploadedFile($resource, 25);
        $this->assertInstanceOf(UploadedFileInterface::class, $upload);
        $upload->getStream()->close();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToUploadNotAFileOrStreamResource()
    {
        $upload = new UploadedFile(31337, 1024);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToGiveInvalidUploadErrorValue()
    {
        $file = \tempnam(\sys_get_temp_dir(), "shit");
        $stream = new Stream($file, "wb+");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $len = $stream->write("same shit, different day.");
        $this->assertEquals(25, $len);
        $stream->close();
        $upload = new UploadedFile($file, 25, 31337);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTryToGetCurrentStreamWithNotOKUploadStatus()
    {
        $file = \tempnam(\sys_get_temp_dir(), "shit");
        $stream = new Stream($file, "wb+");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $len = $stream->write("same shit, different day.");
        $this->assertEquals(25, $len);
        $stream->close();
        $upload = new UploadedFile($file, $len, \UPLOAD_ERR_NO_FILE);
        $this->assertInstanceOf(UploadedFileInterface::class, $upload);
        $upload->getStream();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTryToMoveUploadedFileWithNotOKUploadStatus()
    {
        $file = \tempnam(\sys_get_temp_dir(), "shit");
        $stream = new Stream($file, "wb+");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $len = $stream->write("same shit, different day.");
        $this->assertEquals(25, $len);
        $stream->close();
        $upload = new UploadedFile($file, $len, \UPLOAD_ERR_NO_FILE);
        $this->assertInstanceOf(UploadedFileInterface::class, $upload);
        $upload->moveTo(\sys_get_temp_dir());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToMoveUploadedFileIntoInvalidDestinationType()
    {
        $file = \tempnam(\sys_get_temp_dir(), "shit");
        $stream = new Stream($file, "wb+");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $len = $stream->write("same shit, different day.");
        $this->assertEquals(25, $len);
        $stream->close();
        $upload = new UploadedFile($file, $len, \UPLOAD_ERR_OK);
        $this->assertInstanceOf(UploadedFileInterface::class, $upload);
        $upload->moveTo(31337);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToMoveUploadedFileIntoEmptyDestination()
    {
        $file = \tempnam(\sys_get_temp_dir(), "shit");
        $stream = new Stream($file, "wb+");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $len = $stream->write("same shit, different day.");
        $this->assertEquals(25, $len);
        $stream->close();
        $upload = new UploadedFile($file, $len, \UPLOAD_ERR_OK);
        $this->assertInstanceOf(UploadedFileInterface::class, $upload);
        $upload->moveTo('');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTryToMoveUploadedFileIntoNonWritableDestination()
    {
        $file = \tempnam(\sys_get_temp_dir(), "shit");
        $stream = new Stream($file, "wb+");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $len = $stream->write("same shit, different day.");
        $this->assertEquals(25, $len);
        $stream->close();
        $upload = new UploadedFile($file, $len);
        $this->assertInstanceOf(UploadedFileInterface::class, $upload);
        $upload->moveTo("/etc");
    }
}

<?php

namespace LilleBitte\Teachdaire\Tests;

use LilleBitte\Teachdaire\Stream;
use Psr\Http\Message\StreamInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class StreamTest extends TestCase
{
    public function testCreateStreamFromFilename()
    {
        $stream = new Stream("php://memory", "rb");
        $this->assertInstanceOf(StreamInterface::class, $stream);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateStreamFromNonexistingFilename()
    {
        $stream = new Stream("./nonexistent_file", "rb");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateStreamFromInvalidResourceHandleNorString()
    {
        $stream = new Stream(31337, "rb");
    }

    public function testCanSerializeNonReadableStream()
    {
        $stream = new Stream(
            "php://stdout",
            "wb"
        );

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEmpty((string)$stream);
    }

    public function testCanSerializeDetachedStream()
    {
        $stream = new Stream("php://stdin");

        $this->assertInstanceOf(StreamInterface::class, $stream);

        $stream->close();

        $this->assertEmpty((string)$stream);
    }

    public function testTryToCloseClosedHandler()
    {
        $stream = new Stream("php://stdin");

        $this->assertInstanceOf(StreamInterface::class, $stream);

        $stream->close();
        $stream->close();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTryToGetCurrentStreamPositionOnClosedStream()
    {
        $stream = new Stream("php://memory");

        $this->assertInstanceOf(StreamInterface::class, $stream);

        $stream->close();
        $stream->tell();
    }

    public function testTryToGetCurrentStreamPositionOnReadWriteModeStream()
    {
        $stream = new Stream(
            \tempnam(\sys_get_temp_dir(), 'shit'),
            "wb+"
        );

        $this->assertInstanceOf(StreamInterface::class, $stream);

        $stream->write("this is a shit.");
        $stream->seek(1024, \SEEK_END);
        $stream->tell();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTryToGetCurrentStreamPositionOnOverboundStreamLookup()
    {
        $stream = new Stream("php://memory");

        $this->assertInstanceOf(StreamInterface::class, $stream);

        $stream->seek(4096);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToSeekOnCurrentStreamWithInvalidWhenceValue()
    {
        $stream = new Stream(
            \tempnam(\sys_get_temp_dir(), "shit"),
            "wb"
        );

        $this->assertInstanceOf(StreamInterface::class, $stream);

        $stream->write("this is a text.");
        $stream->seek(1024, 31337);
    }

    public function testTryToDetermineCurrentStreamIsWritableOnClosedStream()
    {
        $stream = new Stream(
            \tempnam(\sys_get_temp_dir(), "shit"),
            "wb"
        );

        $this->assertInstanceOf(StreamInterface::class, $stream);

        $stream->close();
        $this->assertFalse($stream->isWritable());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTryToWriteDataToCurrentClosedStream()
    {
        $stream = new Stream("php://memory");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $stream->close();
        $stream->write("same shit, different day.");
    }

    public function testTryToDetermineCurrentStreamIsReadableOnClosedStream()
    {
        $stream = new Stream("php://memory");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $stream->close();
        $this->assertFalse($stream->isReadable());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTryToReadDataToCurrentClosedStream()
    {
        $stream = new Stream("php://memory");
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $stream->close();
        $stream->read(1024);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTryToGetOverallContentsFromCurrentlyClosedStream()
    {
        $stream = new Stream(
            \tempnam(\sys_get_temp_dir(), "shit"),
            "wb+"
        );
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals(8, $stream->write("aaaabbbb"));
        $stream->close();
        $stream->getContents();
    }

    public function testCanGetAllMetadataIfSuppliedKeyIsNull()
    {
        $stream = new Stream(
            \tempnam(\sys_get_temp_dir(), "shit"),
            "wb+"
        );
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertNotEmpty($stream->getMetadata());
        $stream->close();
    }

    public function testTryToGetMetadataWithNonexistingKey()
    {
        $stream = new Stream(
            \tempnam(\sys_get_temp_dir(), "shit"),
            "wb+"
        );
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertNull($stream->getMetadata("nonexisting-key"));
        $stream->close();
    }

    public function testTryToGetMetadataWithValidKey()
    {
        $stream = new Stream(
            \tempnam(\sys_get_temp_dir(), "shit"),
            "wb+"
        );
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertNotEmpty($stream->getMetadata("mode"));
        $this->assertInternalType("string", $stream->getMetadata("mode"));
        $stream->close();
    }
}

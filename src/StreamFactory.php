<?php

namespace LilleBitte\Teachdaire;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\StreamFactoryInterface;
use LilleBitte\Teachdaire\Stream;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class StreamFactory implements StreamFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = new Stream("php://memory", "rb+");
        $stream->write($content);
        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new Stream($filename, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}

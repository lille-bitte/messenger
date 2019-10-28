<?php

namespace LilleBitte\Messenger\Tests\Integration;

use Http\Psr7Test\StreamIntegrationTest;
use LilleBitte\Messenger\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class StreamTest extends StreamIntegrationTest
{
    /**
     * {@inheritdoc}
     */
    public function createStream($data)
    {
        if ($data instanceof StreamInterface) {
            return $data;
        }

        return new Stream($data);
    }
}

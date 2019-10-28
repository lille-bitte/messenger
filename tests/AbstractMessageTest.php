<?php

namespace LilleBitte\Teachdaire\Tests;

use Psr\Http\Message\MessageInterface;
use LilleBitte\Teachdaire\AbstractMessage;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class AbstractMessageTest extends TestCase
{
    public function testIsInstanceOfAbstractMessage()
    {
        $me = new MessageExtender();
        $this->assertInstanceOf(AbstractMessage::class, $me);
    }

    public function testIsInstanceOfMessageInterface()
    {
        $me = new MessageExtender();
        $this->assertInstanceOf(MessageInterface::class, $me);
    }
}

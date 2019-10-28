<?php

namespace LilleBitte\Teachdaire\Tests;

use LilleBitte\Teachdaire\Response;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ResponseTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInstantiateWithNewStatus()
    {
        $res = new Response();

        $this->assertInstanceOf(ResponseInterface::class, $res);

        $res = $res->withStatus(
            201,
            null
        );
    }
}

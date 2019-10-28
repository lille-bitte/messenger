<?php

namespace LilleBitte\Teachdaire\Tests;

use LilleBitte\Teachdaire\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ResponseFactoryTest extends TestCase
{
    public function testCanInstantiate()
    {
        $res = (new ResponseFactory)
            ->createResponse(
                201,
                "same shit, different day."
            );

        $this->assertInstanceOf(ResponseInterface::class, $res);
    }
}

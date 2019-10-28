<?php

namespace LilleBitte\Teachdaire\Tests;

use LilleBitte\Teachdaire\RequestFactory;
use Psr\Http\Message\RequestInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class RequestFactoryTest extends TestCase
{
    public function testCanInstantiateRequestObject()
    {
        $req = (new RequestFactory)
            ->createRequest(
                "http://localhost:7010/a/b/c",
                "GET"
            );

        $this->assertInstanceOf(RequestInterface::class, $req);
    }
}

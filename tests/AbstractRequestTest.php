<?php

namespace LilleBitte\Teachdaire\Tests;

use Psr\Http\Message\RequestInterface;
use LilleBitte\Teachdaire\AbstractRequest;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class AbstractRequestTest extends TestCase
{
    public function testIsInstanceOfAbstractRequest()
    {
        $re = new RequestExtender();
        $this->assertInstanceOf(AbstractRequest::class, $re);
    }

    public function testIsInstanceOfRequestInterface()
    {
        $re = new RequestExtender();
        $this->assertInstanceOf(RequestInterface::class, $re);
    }
}

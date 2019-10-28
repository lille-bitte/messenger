<?php

namespace LilleBitte\Teachdaire;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\RequestFactoryInterface;
use LilleBitte\Teachdaire\Request;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class RequestFactory implements RequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request($uri, $method);
    }
}

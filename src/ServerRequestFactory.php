<?php

namespace LilleBitte\Messenger;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use LilleBitte\Messenger\ServerRequest;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return (new ServerRequest(null, null, $serverParams))
            ->withMethod($method)
            ->withUri($uri);
    }
}

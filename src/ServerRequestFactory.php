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
    public static function createFromGlobals(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null
    ): ServerRequestInterface {
        $server = $server ?? $_SERVER;
        $files = $files ?? $_FILES;
        $cookies = $cookies ?? $_COOKIE;
        $query = $query ?? $_GET;
        $body = $body ?? $_POST;

        return new ServerRequest(
            $server,
            $files,
            getUriFromServer($server),
            getMethodFromServer($server),
            'php://input',
            [],
            $cookies,
            $query,
            getProtocolVersionFromServer($server)
        );
    }

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

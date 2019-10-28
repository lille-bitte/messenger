<?php

namespace LilleBitte\Messenger;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use LilleBitte\Messenger\Response;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createResponse(int $code = 200, $reasonPhrase = ''): ResponseInterface
    {
        return (new Response())
            ->withStatus($code, $reasonPhrase);
    }
}

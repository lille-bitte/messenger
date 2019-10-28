<?php

namespace LilleBitte\Teachdaire;

use Psr\Http\Message\RequestInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class AbstractRequest extends AbstractMessage implements RequestInterface
{
    use RequestTrait;

    public function __construct(
        $uri = null,
        $method = null,
        $headers = [],
        $body = 'php://memory',
        $version = '1.1'
    ) {
        $this->initialize($uri, $method, $headers, $body);
    }
}

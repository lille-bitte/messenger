<?php

namespace LilleBitte\Messenger;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;
use LilleBitte\Messenger\Uri;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}

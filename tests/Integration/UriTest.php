<?php

namespace LilleBitte\Teachdaire;

use Http\Psr7Test\UriIntegrationTest;
use LilleBitte\Teachdaire\Uri;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class UriTest extends UriIntegrationTest
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri)
    {
        return new Uri($uri);
    }
}

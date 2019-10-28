<?php

namespace LilleBitte\Teachdaire\Tests\Integration;

use Http\Psr7Test\ServerRequestIntegrationTest;
use LilleBitte\Teachdaire\ServerRequest;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ServerRequestTest extends ServerRequestIntegrationTest
{
    /**
     * {@inheritdoc}
     */
    public function createSubject()
    {
        return new ServerRequest($_SERVER);
    }
}

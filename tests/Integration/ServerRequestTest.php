<?php

namespace LilleBitte\Messenger\Tests\Integration;

use Http\Psr7Test\ServerRequestIntegrationTest;
use LilleBitte\Messenger\ServerRequest;

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

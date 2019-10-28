<?php

namespace LilleBitte\Messenger\Tests\Integration;

use Http\Psr7Test\RequestIntegrationTest;
use LilleBitte\Messenger\Request;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class RequestTest extends RequestIntegrationTest
{
    /**
     * {@inheritdoc}
     */
    public function createSubject()
    {
        return new Request('/', 'GET');
    }
}

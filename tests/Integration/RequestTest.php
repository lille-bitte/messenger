<?php

namespace LilleBitte\Teachdaire\Tests\Integration;

use Http\Psr7Test\RequestIntegrationTest;
use LilleBitte\Teachdaire\Request;

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

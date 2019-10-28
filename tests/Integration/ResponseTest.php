<?php

namespace LilleBitte\Teachdaire\Tests\Integration;

use Http\Psr7Test\ResponseIntegrationTest;
use LilleBitte\Teachdaire\Response;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ResponseTest extends ResponseIntegrationTest
{
    /**
     * {@inheritdoc}
     */
    public function createSubject()
    {
        return new Response();
    }
}

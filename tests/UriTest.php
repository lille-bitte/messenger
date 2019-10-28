<?php

namespace LilleBitte\Messenger\Tests;

use LilleBitte\Messenger\Uri;
use Psr\Http\Message\UriInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class UriTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToInstantiateImmutablyWithInvalidHostDataType()
    {
        $uri = (new Uri())
            ->withHost(31337);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToInstantiateImmutablyWithInvalidPortDataType()
    {
        $uri = (new Uri())
            ->withPort(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToInstantiateImmutablyWithInvalidPathDataType()
    {
        $uri = (new Uri())
            ->withPath(31337);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTryToInstantiateImmutablyWithInvalidQueryDataType()
    {
        $uri = (new Uri())
            ->withQuery(false);
    }

    public function testCanGetQualifiedPathWithoutSlashPrefix()
    {
        $uri = (new Uri())
            ->withPath("a/b/c");

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals("/a/b/c", (string)$uri);
    }

    public function testCanGetEmptyPathWhileFiltering()
    {
        $uri = (new Uri("/\xff\xff\xff"))
            ->withQuery("foo=bar");

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals("/?foo=bar", (string)$uri);
    }
}

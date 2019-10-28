<?php

namespace LilleBitte\Messenger\Tests;

use Psr\Http\Message\RequestInterface;
use LilleBitte\Messenger\Request;
use LilleBitte\Messenger\Uri;
use LilleBitte\Messenger\Stream;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class RequestTest extends TestCase
{
    public function testIsInstanceOfRequest()
    {
        $req = new Request();
        $this->assertInstanceOf(Request::class, $req);
    }

    public function testIsInstanceOfRequestInterface()
    {
        $req = new Request();
        $this->assertInstanceOf(RequestInterface::class, $req);
    }

    public function testInstantiateWithNullMethod()
    {
        $req = new Request(
            "/a/b/c/d?foo=1&bar=2"
        );

        $this->assertInstanceOf(Request::class, $req);
        $this->assertEquals("GET", $req->getMethod());
    }

    public function testInstantiateWithNotNullMethod()
    {
        $req = new Request(
            "/a/b/c/d?foo=1&bar=2",
            "POST"
        );

        $this->assertInstanceOf(Request::class, $req);
        $this->assertEquals("POST", $req->getMethod());
    }

    public function testInstantiateWithHostHeader()
    {
        $req = new Request(
            null,
            null,
            ['host' => ['localhost:8000']]
        );

        $this->assertInstanceOf(Request::class, $req);
        $this->assertEquals("localhost:8000", $req->getHeaderLine('host'));
    }

    public function testInstantiateWithURIString()
    {
        $req = new Request(
            "http://localhost:7000/a/b/c/d?foo=1&bar=2"
        );

        $this->assertInstanceOf(Request::class, $req);
        $this->assertEquals("localhost:7000", $req->getHeaderLine('host'));
    }

    public function testInstantiateWithURIObject()
    {
        $req = new Request(
            new Uri("http://localhost:7000/a/b/c/d?foo=1&bar=2")
        );

        $this->assertInstanceOf(Request::class, $req);
    }

    public function testGetRequestTargetWithoutQueryString()
    {
        $req = new Request(
            new Uri("http://localhost:7000/a/b/c/d")
        );

        $this->assertInstanceOf(Request::class, $req);
        $this->assertEquals("/a/b/c/d", $req->getRequestTarget());
    }

    public function testGetRequestTargetWithQueryString()
    {
        $req = new Request(
            new Uri("http://localhost:7000/a/b/c/d?foo=1&bar=2")
        );

        $this->assertInstanceOf(Request::class, $req);
        $this->assertEquals("/a/b/c/d?foo=1&bar=2", $req->getRequestTarget());
    }

    public function testInstantiateWithImmutableURIObject()
    {
        $req = (new Request())
            ->withUri(new Uri("http://localhost:8000"));

        $this->assertInstanceOf(Request::class, $req);
        $this->assertEquals(8000, $req->getUri()->getPort());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInstantiateWithInvalidHeaderValue()
    {
        $req = new Request(
            new Uri("http://localhost:7000/a/b/c?foo=1"),
            null,
            ['invalid-header' => "this\tis\ra\nshit."]
        );
    }

    public function testInstantiateWithStreamObject()
    {
        $req = new Request(
            new Uri("http://localhost:7010/a/b/c"),
            "PUT",
            [],
            new Stream("php://memory")
        );

        $this->assertInstanceOf(Request::class, $req);
    }

    public function testInstantiateWithRedeclareExistingHeader()
    {
        $req = (new Request(
            "http://localhost:7010/a/b/c",
            "PATCH"
        ));

        $this->assertInstanceOf(Request::class, $req);
        $this->assertEquals("localhost:7010", $req->getHeaderLine("host"));

        $req = $req->withHeader("host", "localhost:31337");

        $this->assertInstanceOf(Request::class, $req);
        $this->assertEquals("localhost:31337", $req->getHeaderLine("host"));
    }

    public function testInstantiateWithForceRemovingNonexistingHeader()
    {
        $req = new Request(
            "http://localhost:7010/a/b/c",
            "DELETE"
        );

        $this->assertInstanceOf(Request::class, $req);

        $req = $req->withoutHeader("x-nonexisting-header");

        $this->assertInstanceOf(Request::class, $req);
    }
}

<?php

namespace LilleBitte\Teachdaire\Tests;

use LilleBitte\Teachdaire\ServerRequest;
use LilleBitte\Teachdaire\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ServerRequestTest extends TestCase
{
    public function testCanGetUploadedFiles()
    {
        $req = new ServerRequest(
            [],
            [
                new UploadedFile("foo.0.txt", 1024, \UPLOAD_ERR_NO_FILE),
                new UploadedFile("foo.1.txt", 1024, \UPLOAD_ERR_NO_FILE)
            ]
        );

        $this->assertInstanceOf(ServerRequestInterface::class, $req);
        $res = $req->getUploadedFiles();
        $this->assertEquals(2, \count($res));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInstantiateWithInvalidUploadedFilesStructure()
    {
        $req = new ServerRequest(
            []
        );

        $this->assertInstanceOf(ServerRequestInterface::class, $req);

        $req = $req->withUploadedFiles(["foo", "bar"]);
    }

    public function testInstantiateWithValidUploadedFilesStructure()
    {
        $req = new ServerRequest([]);

        $this->assertInstanceOf(ServerRequestInterface::class, $req);

        $req = $req->withUploadedFiles(
            [
                new UploadedFile("foo.0.txt", 1024, \UPLOAD_ERR_NO_FILE),
                new UploadedFile("foo.1.txt", 1024, \UPLOAD_ERR_NO_FILE)
            ]
        );

        $this->assertInstanceOf(ServerRequestInterface::class, $req);
    }

    public function testInstantiateWithInvalidRecursiveUploadedFilesStructure()
    {
        $req = new ServerRequest([]);

        $this->assertInstanceOf(ServerRequestInterface::class, $req);

        $req = $req->withUploadedFiles(
            [
                [
                    new UploadedFile("foo.1.txt", 1024, \UPLOAD_ERR_NO_FILE),
                    new UploadedFile("foo.2.txt", 1024, \UPLOAD_ERR_NO_FILE)
                ]
            ]
        );

        $this->assertInstanceOf(ServerRequestInterface::class, $req);
    }
}

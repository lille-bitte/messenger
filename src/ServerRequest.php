<?php

namespace LilleBitte\Teachdaire;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ServerRequest extends AbstractRequest implements ServerRequestInterface
{
    /**
     * @var array
     */
    private $server = [];

    /**
     * @var array
     */
    private $cookie = [];

    /**
     * @var array
     */
    private $query = [];

    /**
     * @var array
     */
    private $uploaded = [];

    /**
     * @var array
     */
    private $parsedBody = [];

    /**
     * @var array
     */
    private $attributes = [];

    public function __construct(
        $serverParams = [],
        $uploadedFiles = [],
        $uri = null,
        string $method = null,
        $body = 'php://memory',
        $headers = [],
        $cookieParams = [],
        $queryParams = [],
        $parsedBody = null,
        $version = '1.1'
    ) {
        $this->assertUploadedFiles($uploadedFiles);

        $this->initialize($uri, $method, $headers, $body);
        $this->server = $serverParams;
        $this->cookie = $cookieParams;
        $this->query = $queryParams;
        $this->uploaded = $uploadedFiles;
        $this->parsedBody = $parsedBody;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->server;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {
        return $this->cookie;
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $q = clone $this;
        $q->cookie = $cookies;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $q = clone $this;
        $q->query = $query;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles()
    {
        return $this->uploaded;
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $this->assertUploadedFiles($uploadedFiles);
        $q = clone $this;
        $q->uploaded = $uploadedFiles;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        if (!is_object($data) && !is_array($data) && null !== $data) {
            throw new \InvalidArgumentException(
                "Deserialized request data must be an array, object, or null."
            );
        }

        $q = clone $this;
        $q->parsedBody = $data;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value)
    {
        $q = clone $this;
        $q->attributes[$name] = $value;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name)
    {
        $q = clone $this;
        unset($q->attributes[$name]);
        return $q;
    }

    private function assertUploadedFiles($uploadedFiles)
    {
        foreach ($uploadedFiles as $el) {
            if (is_array($el)) {
                $this->assertUploadedFiles($el);
                continue;
            }

            if (!($el instanceof UploadedFileInterface)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        "Uploaded file array leaf must be an instance of %s",
                        UploadedFileInterface::class
                    )
                );
            }
        }
    }
}

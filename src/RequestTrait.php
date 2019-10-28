<?php

namespace LilleBitte\Teachdaire;

use Psr\Http\Message\UriInterface;
use LilleBitte\Teachdaire\Uri;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait RequestTrait
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var string|null
     */
    private $requestTarget;

    protected function initialize(
        $uri = null,
        $method = null,
        $headers = [],
        $body = 'php://memory'
    ) {
        $this->method = $method === null ? 'GET' : $method;
        $this->createUri($uri);
        $this->body = $this->createOrGetStream($body, 'wb+');
        $this->rearrangeHeader($headers);
        $this->setHostHeader();
    }

    private function setHostHeader()
    {
        if (!$this->getHeader('Host') && $this->uri->getHost()) {
            $host = $this->uri->getHost();

            if ($this->uri->getPort()) {
                $host .= ':' . $this->uri->getPort();
            }

            $this->headerNames['host'] = 'Host';
            $this->headers['Host'] = [$host];
        }
    }

    private function createUri($uri)
    {
        if (null === $uri) {
            $this->uri = new Uri();
        }

        if ($uri instanceof UriInterface) {
            $this->uri = $uri;
        }

        if (is_string($uri)) {
            $this->uri = new Uri($uri);
        }
    }

    private function composeOriginForm()
    {
        $orig = $this->uri->getPath();

        if ($this->uri->getQuery()) {
            $orig .= '?' . $this->uri->getQuery();
        }

        return empty($orig) ? '/' : $orig;
    }

    private function assertMethod($method)
    {
        if (!is_string($method)) {
            throw new \InvalidArgumentException(
                "HTTP method must be a string."
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        return $this->composeOriginForm();
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget)
    {
        $q = clone $this;
        $q->requestTarget = $requestTarget;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method)
    {
        $this->assertMethod($method);

        $q = clone $this;
        $q->method = $method;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $q = clone $this;
        $q->uri = $uri;

        if ($preserveHost && $this->hasHeader('Host')) {
            return $q;
        }

        if (!$q->uri->getHost()) {
            return $q;
        }

        $host = $q->uri->getHost();

        if ($q->uri->getPort()) {
            $host .= ':' . $q->uri->getPort();
        }

        $q->headerNames['host'] = 'Host';
        $q->headers['Host'] = [$host];

        return $q;
    }
}

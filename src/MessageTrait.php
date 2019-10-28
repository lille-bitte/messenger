<?php

namespace LilleBitte\Messenger;

use Psr\Http\Message\StreamInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait MessageTrait
{
    /**
     * @var string
     */
    protected $version = "1.1";

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $headerNames = [];

    /**
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $body;

    private function assertHeader($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(
                "Header name must be a string."
            );
        }

        if (!preg_match("/^[0-9a-zA-Z!#\$\%\&'\*\+\-\.\^\_`\|\~]+$/", $name)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid header name '%s'",
                    $name
                )
            );
        }
    }

    private function filterHeaderValues($values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        if ($values === []) {
            throw new \InvalidArgumentException(
                "Header values cannot be empty array."
            );
        }

        return array_map(function ($val) {
            if (!is_string($val) && !is_numeric($val)) {
                throw new \InvalidArgumentException(
                    "Header value must be string or numeric type."
                );
            }

            if (preg_match("/(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))/", $val) ||
                preg_match("/[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]/", $val)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        "'%s' is not valid header value.",
                        $val
                    )
                );
            }

            return (string)($val);
        }, array_values($values));
    }

    protected function assertBodyStream($body)
    {
        if (!($body instanceof StreamInterface)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Stream body must be instance of %s.",
                    StreamInterface::class
                )
            );
        }
    }

    protected function createOrGetStream($body, $mode)
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        return new Stream($body, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        $q = clone $this;
        $q->version = $version;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return [];
        }

        return $this->headers[$this->headerNames[strtolower($name)]];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        if (!$this->hasHeader($name)) {
            return '';
        }

        return join(', ', $this->getHeader($name));
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        $this->assertHeader($name);
        $value = $this->filterHeaderValues($value);

        $q = clone $this;

        // free up memory by unsetting existing value
        // before resetting it to new value.
        if ($q->hasHeader($name)) {
            unset($q->headers[$q->headerNames[strtolower($name)]]);
        }

        $q->headerNames[strtolower($name)] = $name;
        $q->headers[$name] = $value;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $this->assertHeader($name);

        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        $value = $this->filterHeaderValues($value);

        $q = clone $this;
        $q->headers[$this->headerNames[strtolower($name)]] = \array_merge(
            $this->headers[$this->headerNames[strtolower($name)]],
            is_array($value) ? $value : [$value]
        );

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        $this->assertHeader($name);

        if (!$this->hasHeader($name)) {
            return clone $this;
        }

        $norm = strtolower($name);
        $orig = $this->headerNames[$norm];

        $q = clone $this;
        unset($q->headerNames[$norm], $q->headers[$orig]);
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $this->assertBodyStream($body);

        $q = clone $this;
        $q->body = $body;

        return $q;
    }

    public function normalizeHeaderName($name)
    {
        $part = \array_map(
            function ($q) {
                return \ucfirst($q);
            },
            \explode('-', $name)
        );

        return \join('-', $part);
    }

    public function rearrangeHeader($header = [])
    {
        foreach ($header as $name => $value) {
            $this->headerNames[strtolower($name)] = $this->normalizeHeaderName($name);
            $this->headers[$this->normalizeHeaderName($name)] = $this->filterHeaderValues($value);
        }
    }
}

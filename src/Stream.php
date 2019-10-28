<?php

namespace LilleBitte\Teachdaire;

use Psr\Http\Message\StreamInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Stream implements StreamInterface
{
    /**
     * @var resource|null
     */
    private $handler;

    /**
     * @var integer|null
     */
    private $size;

    /**
     * @var array
     */
    private $metadata;

    public function __construct($name, $mode = 'r')
    {
        $this->createStream($name, $mode);
        $this->setStreamMetadata();
    }

    private function resetStreamHandler()
    {
        $this->handler = null;
        $this->metadata = null;
    }

    private function createStream($name, $mode)
    {
        $err = null;
        $stream = $name;

        if (is_string($name)) {
            \set_error_handler(function ($enum, $estr) use (&$err) {
                $err = $enum;
            }, \E_WARNING);

            // fopen() in PHP always returning
            // E_WARNING error code.
            $stream = fopen($name, $mode);

            \restore_error_handler();
        }

        if ($err) {
            throw new \InvalidArgumentException(
                "Invalid stream reference provided."
            );
        }

        if (!is_resource($stream) || get_resource_type($stream) !== 'stream') {
            throw new \InvalidArgumentException(
                "Invalid stream provided; must be a string stream identifier or stream resource."
            );
        }

        $this->handler = $stream;
    }

    private function setStreamMetadata()
    {
        $this->metadata = \stream_get_meta_data($this->handler);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\RuntimeException $e) {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (!$this->handler) {
            return;
        }

        $resource = $this->detach();
        fclose($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $resource = $this->handler;
        $this->resetStreamHandler();
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $stat = \fstat($this->handler);

        return (false !== $stat)
            ? $stat['size']
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        if (null === $this->handler) {
            throw new \RuntimeException(
                "Stream handler must be valid."
            );
        }

        $pos = \ftell($this->handler);

        if (false === $pos) {
            throw new \RuntimeException(
                "Failed to get current stream position."
            );
        }

        return $pos;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return feof($this->handler);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        if (null === $this->handler) {
            return false;
        }

        return $this->metadata['seekable'];
    }

    private function checkWhence($whence)
    {
        if (!in_array($whence, [\SEEK_SET, \SEEK_CUR, \SEEK_END], true)) {
            throw new \InvalidArgumentException(
                "Invalid whence value."
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = \SEEK_SET)
    {
        if (false === $this->isSeekable()) {
            throw new \RuntimeException(
                "Current stream handler is not seekable."
            );
        }

        $this->checkWhence($whence);

        if (fseek($this->handler, $offset, $whence) == -1) {
            throw new \RuntimeException(
                sprintf(
                    "Seek on offset: %d on current stream handler failed.",
                    $offset
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        if (null === $this->handler) {
            return false;
        }

        return (
            strstr($this->metadata['mode'], 'x')
            || strstr($this->metadata['mode'], 'w')
            || strstr($this->metadata['mode'], 'c')
            || strstr($this->metadata['mode'], 'a')
            || strstr($this->metadata['mode'], '+')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException(
                "Current stream handler is not writable."
            );
        }

        $result = fwrite($this->handler, $string);

        if (false === $result) {
            throw new \RuntimeException(
                "Failed to write data on current stream handler."
            );
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        if (null === $this->handler) {
            return false;
        }

        return (strstr($this->metadata['mode'], 'r') || strstr($this->metadata['mode'], '+'));
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if (false === $this->isReadable()) {
            throw new \RuntimeException(
                "Current stream handler is not readable."
            );
        }

        $result = fread($this->handler, $length);

        if (false === $result) {
            throw new \RuntimeException(
                "Failed to read from current stream handler."
            );
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        if (false === $this->isReadable()) {
            throw new \RuntimeException(
                "Current stream handler is not readable."
            );
        }

        $result = \stream_get_contents($this->handler);

        if (false === $result) {
            throw new \RuntimeException(
                "Failed to read overall data from current stream handler."
            );
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        if (null === $key) {
            return $this->metadata;
        }

        if (!array_key_exists($key, $this->metadata)) {
            return null;
        }

        return $this->metadata[$key];
    }
}

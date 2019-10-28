<?php

namespace LilleBitte\Teachdaire;

use Psr\Http\Message\UriInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Uri implements UriInterface
{
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $host;

    /**
     * @var integer|null
     */
    private $port;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $fragment;

    /**
     * @var array
     */
    private $allowedSchemes = [
        'http' => 80,
        'https' => 443
    ];

    /**
     * RFC 3986 Appendix A: Collected ABNF for URI
     *
     * @var array
     */
    private $filteringPattern = [
        'unreserved' => "a-zA-Z0-9\-\.\_\~",
        'sub-delims' => "\!\$\&\'\(\)\*\+\,\;\=",
        'pct-encoded' => "%(?![a-fA-F0-9]{2})"
    ];

    public function __construct($uri = '')
    {
        if ($uri === '') {
            $this->resetUriComponents();
            return;
        }

        $this->parseUri($uri);
    }

    private function resetUriComponents()
    {
        $this->scheme = '';
        $this->host = '';
        $this->port = null;
        $this->user = '';
        $this->password = '';
        $this->path = '';
        $this->query = '';
        $this->fragment = '';
    }

    private function parseUri($uri)
    {
        $parts = \parse_url($uri);

        $this->scheme = (isset($parts['scheme']) ? $parts['scheme'] : '');
        $this->host = (isset($parts['host']) ? \strtolower($parts['host']) : '');
        $this->port = (isset($parts['port']) ? $parts['port'] : null);
        $this->user = (isset($parts['user']) ? $parts['user'] : '');
        $this->password = (isset($parts['pass']) ? $parts['pass'] : '');
        $this->path = (isset($parts['path']) ? $this->filterPath($parts['path']) : '');
        $this->query = (isset($parts['query']) ? $this->filterQuery($parts['query']) : '');
        $this->fragment = (isset($parts['fragment']) ? $parts['fragment'] : '');
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority()
    {
        $authority = '';

        if ($this->getUserInfo() !== '') {
            $authority = $this->getUserInfo() . '@';
        }

        $authority .= $this->getHost();

        if (null !== $this->getPort()) {
            if ($this->isNonStandardPort($this->getScheme(), $this->getPort())) {
                $authority .= ':' . $this->getPort();
            }
        }

        return $authority;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo()
    {
        $userInfo = $this->user;

        if ($this->password !== '') {
            $userInfo .= ':' . $this->password;
        }

        return $userInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->isNonStandardPort($this->getScheme(), $this->port)
            ? $this->port
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme)
    {
        if (!is_string($scheme)) {
            throw new \InvalidArgumentException(
                "URI scheme must be a string."
            );
        }

        $q = clone $this;
        $q->scheme = $scheme;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $q = clone $this;
        $q->user = $user;
        $q->password = $password;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host)
    {
        if (!is_string($host)) {
            throw new \InvalidArgumentException(
                "URI host must be a string."
            );
        }

        $q = clone $this;
        $q->host = $host;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port)
    {
        if (!is_int($port)) {
            throw new \InvalidArgumentException(
                "URI port must be an integer."
            );
        }

        $q = clone $this;
        $q->port = $port;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(
                "URI path must be a string."
            );
        }

        $q = clone $this;
        $q->path = $path;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query)
    {
        if (!is_string($query)) {
            throw new \InvalidArgumentException(
                "URI query string must be a string."
            );
        }

        $q = clone $this;
        $q->query = $query;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment)
    {
        $q = clone $this;
        $q->fragment = $fragment;
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $fullUri = '';

        if ($this->getScheme() !== '') {
            $fullUri .= $this->getScheme() . ':';
        }

        if ($this->getAuthority() !== '') {
            $fullUri .= '//' . $this->getAuthority();
        }

        if ($this->getPath() !== '') {
            if (0 !== stripos($this->getPath(), '/')) {
                $fullUri .= '/' . $this->getPath();
            } else {
                $fullUri .= '/' . \ltrim($this->getPath(), '/');
            }
        }

        if ($this->getQuery() !== '') {
            $fullUri .= '?' . $this->getQuery();
        }

        if ($this->getFragment() !== '') {
            $fullUri .= '#' . $this->getFragment();
        }

        return $fullUri;
    }

    private function isNonStandardPort($scheme, $port)
    {
        return !isset($this->allowedSchemes[$scheme]) || $port !== $this->allowedSchemes[$scheme];
    }

    private function filterPath($path)
    {
        $path = \preg_replace_callback(
            '/(?:[^' .
            $this->filteringPattern['unreserved'] .
            $this->filteringPattern['sub-delims'] .
            '\/\%\@\:]+|' .
            $this->filteringPattern['pct-encoded'] .
            ')/u',
            [$this, 'encodeUriPortion'],
            $path
        );

        if ('' === $path) {
            return $path;
        }

        if ($path[0] !== '/') {
            return $path;
        }

        return '/' . ltrim($path, '/');
    }

    private function filterQuery($query)
    {
        if ('' !== $query && strpos($query, '?') === 0) {
            $query = substr($query, 1);
        }

        $parts = \explode('&', $query);

        foreach ($parts as $a => $b) {
            $tmp = explode('=', $b, 2);
            list($key, $value) = [$tmp[0], !isset($tmp[1]) ? null : $tmp[1]];

            if (!isset($value)) {
                $value = null;
            }

            if (null === $value) {
                $parts[$a] = $this->filterQueryOrFragment($key);
                continue;
            }

            $parts[$a] = sprintf(
                "%s=%s",
                $this->filterQueryOrFragment($key),
                $this->filterQueryOrFragment($value)
            );
        }

        return implode('&', $parts);
    }

    private function filterQueryOrFragment($value)
    {
        $path = \preg_replace_callback(
            '/(?:[^' .
            $this->filteringPattern['unreserved'] .
            $this->filteringPattern['sub-delims'] .
            '\/\?\%\@\:]+|' .
            $this->filteringPattern['pct-encoded'] .
            ')/u',
            [$this, 'encodeUriPortion'],
            $value
        );

        return $path;
    }

    private function encodeUriPortion($paths)
    {
        return rawurlencode($paths[0]);
    }
}

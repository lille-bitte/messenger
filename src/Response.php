<?php

namespace LilleBitte\Teachdaire;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Response extends AbstractMessage implements ResponseInterface
{
    /**
     * @var integer
     */
    private $statusCode;

    /**
     * @var string
     */
    private $reasonPhrase;

    /**
     * @var array
     */
    private $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    public function __construct($body = 'php://memory', $status = 200, $headers = [])
    {
        $this->setStatusCode($status);
        $this->body = $this->createOrGetStream($body, 'wb+');
        $this->headers = $this->rearrangeHeader($headers);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $q = clone $this;
        $q->setStatusCode($code, $reasonPhrase);
        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    private function setStatusCode($code, $reasonPhrase = '')
    {
        if (!is_integer($code) || !in_array($code, array_keys($this->phrases), true)) {
            throw new \InvalidArgumentException(
                "Invalid HTTP status code."
            );
        }

        if (!is_string($reasonPhrase)) {
            throw new \InvalidArgumentException(
                "HTTP reason phrase must be a string."
            );
        }

        if (!$reasonPhrase && isset($this->phrases[$code])) {
            $this->reasonPhrase = $this->phrases[$code];
        }

        $this->reasonPhrase = $reasonPhrase;
        $this->statusCode = (int)$code;
    }
}

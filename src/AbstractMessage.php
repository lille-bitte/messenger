<?php

namespace LilleBitte\Teachdaire;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
abstract class AbstractMessage implements MessageInterface
{
    use MessageTrait;

    public function __construct()
    {
    }
}

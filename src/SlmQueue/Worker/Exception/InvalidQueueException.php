<?php

namespace SlmQueue\Worker\Exception;

use RuntimeException;
use SlmQueue\Exception\ExceptionInterface;

/**
 * InvalidQueueException
 */
class InvalidQueueException extends RuntimeException implements ExceptionInterface
{
}

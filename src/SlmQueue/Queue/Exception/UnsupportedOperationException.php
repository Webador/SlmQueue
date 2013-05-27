<?php

namespace SlmQueue\Queue\Exception;

use Exception;
use SlmQueue\Exception\ExceptionInterface as SlmQueueExceptionInterface;

/**
 * UnsupportedOperationException
 */
class UnsupportedOperationException extends Exception implements SlmQueueExceptionInterface
{
}

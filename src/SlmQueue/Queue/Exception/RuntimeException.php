<?php

namespace SlmQueue\Queue\Exception;

use RuntimeException as BaseRuntimeException;
use SlmQueue\Exception\ExceptionInterface as SlmQueueExceptionInterface;

/**
 * RuntimeException
 */
class RuntimeException extends BaseRuntimeException implements SlmQueueExceptionInterface
{
}

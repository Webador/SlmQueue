<?php

namespace SlmQueue\Factory\Exception;

use RuntimeException as BaseRuntimeException;
use SlmQueue\Exception\SlmQueueExceptionInterface;

/**
 * RuntimeException
 */
class RuntimeException extends BaseRuntimeException implements SlmQueueExceptionInterface
{
}

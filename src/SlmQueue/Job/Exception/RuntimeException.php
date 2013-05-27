<?php

namespace SlmQueue\Job\Exception;

use RuntimeException as BaseRuntimeException;
use SlmQueue\Exception\SlmQueueExceptionInterface;

/**
 * RuntimeException
 */
class RuntimeException extends BaseRuntimeException implements SlmQueueExceptionInterface
{
}

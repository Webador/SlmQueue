<?php

namespace SlmQueue\Job\Exception;

use RuntimeException as BaseRuntimeException;
use SlmQueue\Exception\ExceptionInterface;

/**
 * RuntimeException
 */
class RuntimeException extends BaseRuntimeException implements ExceptionInterface
{
}

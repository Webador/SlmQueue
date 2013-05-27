<?php

namespace SlmQueue\Controller\Exception;

use SlmQueue\Exception\ExceptionInterface as SlmQueueExceptionInterface;
use RuntimeException;

class WorkerProcessException extends RuntimeException implements SlmQueueExceptionInterface
{
}

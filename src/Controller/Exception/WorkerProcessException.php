<?php

namespace SlmQueue\Controller\Exception;

use RuntimeException;
use SlmQueue\Exception\ExceptionInterface;

class WorkerProcessException extends RuntimeException implements ExceptionInterface
{
}

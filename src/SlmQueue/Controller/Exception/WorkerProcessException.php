<?php

namespace SlmQueue\Controller\Exception;

use SlmQueue\Exception\ExceptionInterface;
use RuntimeException;

class WorkerProcessException extends RuntimeException implements ExceptionInterface
{
}

<?php

namespace SlmQueue\Worker\Exception;

use SlmQueue\Exception\SlmQueueExceptionInterface;
use RuntimeException;

class WorkerProcessException
    extends RuntimeException
    implements SlmQueueExceptionInterface
{
}
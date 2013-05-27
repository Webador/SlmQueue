<?php

namespace SlmQueue\Controller\Exception;

use SlmQueue\Exception\SlmQueueExceptionInterface;
use RuntimeException;

class WorkerProcessException
    extends RuntimeException
    implements SlmQueueExceptionInterface
{
}

<?php

namespace SlmQueue\Controller\Exception;

use SlmQueue\Exception\ExceptionInterface;
use InvalidArgumentException;

class QueueNotFoundException extends InvalidArgumentException implements ExceptionInterface
{
}

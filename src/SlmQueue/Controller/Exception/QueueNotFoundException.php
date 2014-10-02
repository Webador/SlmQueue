<?php

namespace SlmQueue\Controller\Exception;

use InvalidArgumentException;
use SlmQueue\Exception\ExceptionInterface;

class QueueNotFoundException extends InvalidArgumentException implements ExceptionInterface
{
}

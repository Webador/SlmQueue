<?php

namespace SlmQueueTest\Asset;

use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Strategy\AbstractStrategy;

class SimpleStrategy extends AbstractStrategy
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        die();
    }
}

<?php

namespace SlmQueue\Worker\Event;

use Laminas\EventManager\Event;
use SlmQueue\Worker\WorkerInterface;

/**
 * AbstractWorkerEvent
 */
abstract class AbstractWorkerEvent extends Event implements WorkerEventInterface
{
    /**
     * @param string          $name
     * @param WorkerInterface $target
     */
    public function __construct($name, WorkerInterface $target)
    {
        parent::__construct($name, $target);
    }

    /**
     * @inheritdoc
     */
    public function getWorker()
    {
        return $this->getTarget();
    }
}

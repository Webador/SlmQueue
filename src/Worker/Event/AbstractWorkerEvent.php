<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Worker\WorkerInterface;
use Zend\EventManager\Event;

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

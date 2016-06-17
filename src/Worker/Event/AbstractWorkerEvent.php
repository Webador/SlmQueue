<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Worker\WorkerInterface;
use Zend\EventManager\Event;

/**
 * AbstractWorkerEvent
 */
abstract class AbstractWorkerEvent extends Event
{
    /**
     * Various events you can subscribe to
     */
    const EVENT_BOOTSTRAP     = 'bootstrap';
    const EVENT_FINISH        = 'finish';
    const EVENT_PROCESS_QUEUE = 'process.queue';
    const EVENT_PROCESS_JOB   = 'process.job';
    const EVENT_PROCESS_IDLE  = 'process.idle';
    const EVENT_PROCESS_STATE = 'process.state';

    /**
     * @param string          $name
     * @param WorkerInterface $target
     */
    public function __construct($name, WorkerInterface $target)
    {
        parent::__construct($name, $target);
    }
}

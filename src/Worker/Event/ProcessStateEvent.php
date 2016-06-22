<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Worker\WorkerInterface;

/**
 * ProcessStateEvent
 */
class ProcessStateEvent extends AbstractWorkerEvent
{
    /**
     * @param WorkerInterface $target
     */
    public function __construct(WorkerInterface $target)
    {
        parent::__construct(self::EVENT_PROCESS_STATE, $target);
    }
}

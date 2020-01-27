<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Worker\WorkerInterface;

class ProcessStateEvent extends AbstractWorkerEvent
{
    public function __construct(WorkerInterface $target)
    {
        parent::__construct(self::EVENT_PROCESS_STATE, $target);
    }
}

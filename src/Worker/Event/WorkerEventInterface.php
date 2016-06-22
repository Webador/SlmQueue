<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Worker\WorkerInterface;

interface WorkerEventInterface
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
     * @return WorkerInterface
     */
    public function getWorker();
}

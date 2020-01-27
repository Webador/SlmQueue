<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Worker\WorkerInterface;

interface WorkerEventInterface
{
    /**
     * Various events you can subscribe to
     */
    public const EVENT_BOOTSTRAP     = 'bootstrap';
    public const EVENT_FINISH        = 'finish';
    public const EVENT_PROCESS_QUEUE = 'process.queue';
    public const EVENT_PROCESS_JOB   = 'process.job';
    public const EVENT_PROCESS_IDLE  = 'process.idle';
    public const EVENT_PROCESS_STATE = 'process.state';

    /**
     * @return WorkerInterface
     */
    public function getWorker();
}

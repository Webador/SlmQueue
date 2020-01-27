<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\WorkerInterface;

class ProcessIdleEvent extends AbstractWorkerEvent
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    public function __construct(WorkerInterface $target, QueueInterface $queue)
    {
        parent::__construct(self::EVENT_PROCESS_IDLE, $target);

        $this->queue = $queue;
    }

    public function getQueue(): QueueInterface
    {
        return $this->queue;
    }
}

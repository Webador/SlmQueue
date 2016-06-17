<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\WorkerInterface;

/**
 * ProcessIdleEvent
 */
class ProcessIdleEvent extends AbstractWorkerEvent
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * @param WorkerInterface $target
     * @param QueueInterface  $queue
     */
    public function __construct(WorkerInterface $target, QueueInterface $queue)
    {
        parent::__construct(self::EVENT_PROCESS_IDLE, $target);

        $this->queue = $queue;
    }

    /**
     * @return QueueInterface
     */
    public function getQueue()
    {
        return $this->queue;
    }
}

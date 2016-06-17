<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\WorkerInterface;

/**
 * BootstrapEvent
 */
class BootstrapEvent extends AbstractWorkerEvent
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
        parent::__construct(self::EVENT_BOOTSTRAP, $target);

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

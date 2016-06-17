<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\WorkerInterface;

/**
 * ProcessQueueEvent
 */
class ProcessQueueEvent extends AbstractWorkerEvent
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param WorkerInterface $target
     * @param QueueInterface  $queue
     */
    public function __construct(WorkerInterface $target, QueueInterface $queue, array $options = [])
    {
        parent::__construct(self::EVENT_PROCESS_QUEUE, $target);

        $this->queue = $queue;
        $this->options = $options;
    }

    /**
     * @return QueueInterface
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}

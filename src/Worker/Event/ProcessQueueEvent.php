<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\WorkerInterface;

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

    public function __construct(WorkerInterface $target, QueueInterface $queue, array $options = [])
    {
        parent::__construct(self::EVENT_PROCESS_QUEUE, $target);

        $this->queue = $queue;
        $this->options = $options;
    }

    public function getQueue(): QueueInterface
    {
        return $this->queue;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}

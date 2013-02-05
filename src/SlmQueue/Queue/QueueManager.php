<?php

namespace SlmQueue\Queue;

/**
 * The queue manager is responsible to retrieve a single queue by its name
 */
class QueueManager
{
    /**
     * @var QueueInterface[]
     */
    protected $queues;


    /**
     * @param QueueInterface[] $queues
     */
    public function __construct(array $queues)
    {
        $this->queues = $queues;
    }

    /**
     * Get the queue from the queue manager by its name
     *
     * @param  string $name
     * @return QueueInterface|null
     */
    public function getQueue($name)
    {
        return isset($this->queues[$name]) ? $this->queues[$name] : null;
    }
}

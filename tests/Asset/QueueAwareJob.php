<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueInterface;

class QueueAwareJob extends SimpleJob implements QueueAwareInterface
{
    protected $queue;

    /**
     * {@inheritDoc}
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * {@inheritDoc}
     */
    public function setQueue(QueueInterface $queue)
    {
        $this->queue = $queue;
    }
}

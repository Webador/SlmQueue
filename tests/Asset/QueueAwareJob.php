<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Queue\QueueAwareInterface;
use SlmQueue\Queue\QueueInterface;

class QueueAwareJob extends SimpleJob implements QueueAwareInterface
{
    protected $queue;

    public function getQueue(): QueueInterface
    {
        return $this->queue;
    }

    public function setQueue(QueueInterface $queue): void
    {
        $this->queue = $queue;
    }
}

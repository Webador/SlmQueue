<?php

namespace SlmQueue\Queue;

trait QueueAwareTrait
{
    /**
     * @var QueueInterface
     */
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

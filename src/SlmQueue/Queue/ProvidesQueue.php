<?php

namespace SlmQueue\Queue;

trait ProvidesQueue
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * Retrieve the queue
     *
     * @return QueueInterface
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Inject a Queue instance
     *
     * @param  QueueInterface $queue
     * @return void
     */
    public function setQueue(QueueInterface $queue)
    {
        $this->queue = $queue;
    }
}

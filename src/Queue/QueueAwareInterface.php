<?php

namespace SlmQueue\Queue;

interface QueueAwareInterface
{
    /**
     * Retrieve the queue
     *
     * @return QueueInterface
     */
    public function getQueue();

    /**
     * Inject a Queue instance
     *
     * @param QueueInterface $queue
     * @return void
     */
    public function setQueue(QueueInterface $queue);
}

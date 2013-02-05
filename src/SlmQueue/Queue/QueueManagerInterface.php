<?php

namespace SlmQueue;

/**
 * The queue manager is responsible to retrieve a single queue by its name. SlmQueue provides out of the box
 * two queue managers: one for Beanstalk and one for Amazon SQS
 */
interface QueueManagerInterface
{
    /**
     * Get the queue from the queue manager by its name
     *
     * @param  string $name
     * @return QueueInterface
     */
    public function getQueue($name);
}

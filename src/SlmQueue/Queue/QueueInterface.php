<?php

namespace SlmQueue;

/**
 * A queue contains a list of jobs. It can performs various tasks on jobs, like putting a new job,
 * removing it... SlmQueue provides out of the box two queues: one for Beanstalk and one for Amazon SQS
 */
interface QueueInterface
{
    /**
     * Get the name of the queue
     *
     * @return string
     */
    public function getName();
}

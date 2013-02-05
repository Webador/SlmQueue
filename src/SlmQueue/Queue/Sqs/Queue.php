<?php

namespace SlmQueue\Queue\Sqs;

use SlmQueue\AbstractQueue;
use SlmQueue\Job\JobInterface;

/**
 * Provides a basic queue implementation for Amazon SQS
 */
class Queue extends AbstractQueue
{
    /**
     * {@inheritDoc}
     */
    public function push(JobInterface $job, array $options = array())
    {
        // TODO: Implement push() method.
    }

    /**
     * {@inheritDoc}
     */
    public function pop()
    {
        // TODO: Implement pop() method.
    }

    /**
     * {@inheritDoc}
     */
    public function delete(JobInterface $job)
    {
        // TODO: Implement delete() method.
    }
}

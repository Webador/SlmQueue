<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\AbstractQueue;

class SimpleQueue extends AbstractQueue
{
    /**
     * @var array
     */
    protected $queue;


    /**
     * {@inheritDoc}
     */
    public function push(JobInterface $job)
    {
        $this->queue[] = json_encode($job);
    }

    /**
     * {@inheritDoc}
     */
    public function batchPush(array $jobs)
    {
        foreach ($jobs as $job) {
            $this->queue[] = json_encode($job);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function pop()
    {
        return $this->createJob(array_pop($this->queue), array());
    }

    /**
     * {@inheritDoc}
     */
    public function delete(JobInterface $job)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function batchDelete(array $jobs)
    {
    }
}

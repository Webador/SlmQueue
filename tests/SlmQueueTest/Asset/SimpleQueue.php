<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\AbstractQueue;

class SimpleQueue extends AbstractQueue
{
    /**
     * @var array
     */
    protected $jobs;


    /**
     * {@inheritDoc}
     */
    public function push(JobInterface $job)
    {
        $this->jobs[] = $job->jsonSerialize();
    }

    /**
     * {@inheritDoc}
     */
    public function pop()
    {
        return $this->createJob(array_pop($this->jobs), array());
    }

    /**
     * {@inheritDoc}
     */
    public function delete(JobInterface $job)
    {
        foreach ($this->jobs as $key => $value) {
            if ($value->getId() === $job->getId()) {
                unset($this->jobs[$key]);
            }
        }
    }
}

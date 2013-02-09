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
    public function push(JobInterface $job, array $options = array())
    {
        $this->jobs[] = $job->jsonSerialize();
    }

    /**
     * {@inheritDoc}
     */
    public function pop(array $options = array())
    {
        $job = json_decode(array_pop($this->jobs), true);
        return $this->createJob($job['class'], $job['content']);
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

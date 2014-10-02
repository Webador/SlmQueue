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
        $this->jobs[] = $this->serializeJob($job);
    }

    /**
     * {@inheritDoc}
     */
    public function pop(array $options = array())
    {
        $payload = array_pop($this->jobs);
        if (!$payload) {
            return;
        }

        return $this->unserializeJob($payload);
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

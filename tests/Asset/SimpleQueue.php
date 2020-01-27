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
     * @var array
     */
    protected $options;


    /**
     * {@inheritDoc}
     */
    public function push(JobInterface $job, array $options = [])
    {
        $this->jobs[] = $this->serializeJob($job);
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function pop(array $options = [])
    {
        $this->options = $options;

        $payload = array_pop($this->jobs);
        if (! $payload) {
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

    /**
     * Return used options param
     *
     * @return mixed
     */
    public function getUsedOptions()
    {
        return $this->options;
    }
}

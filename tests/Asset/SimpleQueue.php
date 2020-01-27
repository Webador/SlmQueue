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


    public function push(JobInterface $job, array $options = []): void
    {
        $this->jobs[] = $this->serializeJob($job);
        $this->options = $options;
    }

    public function pop(array $options = []): ?JobInterface
    {
        $this->options = $options;

        $payload = array_pop($this->jobs);
        if (! $payload) {
            return null;
        }

        return $this->unserializeJob($payload);
    }

    public function delete(JobInterface $job): void
    {
        foreach ($this->jobs as $key => $value) {
            if ($value->getId() === $job->getId()) {
                unset($this->jobs[$key]);
            }
        }
    }

    /**
     * Return used options param
     */
    public function getUsedOptions(): array
    {
        return $this->options;
    }
}

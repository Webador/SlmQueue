<?php

namespace SlmQueue\Producer;

use SlmQueue\Job\JobInterface;
use SlmQueue\Service\BeanstalkInterface;

abstract class AbstractProducer implements ProducerInterface
{
    /**
     * @var BeanstalkInterface
     */
    protected $beanstalk;

    /**
     * {@inheritDoc}
     */
    public function setBeanstalk(BeanstalkInterface $beanstalk)
    {
        $this->beanstalk = $beanstalk;
        return $this;
    }

    /**
     * Add job to the beanstalk queue
     *
     * @param JobInterface $job      Job to be executed
     * @param int          $priority Priority for this job
     * @param int          $delay    Delay for this given job
     * @param int          $ttr      Time to run for this job
     * @return AbstractProducer
     */
    public function add(JobInterface $job, $priority = null, $delay = null, $ttr = null)
    {
        $this->beanstalk->put($job, $priority, $delay, $ttr);
        return $this;
    }
}

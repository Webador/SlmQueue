<?php

namespace SlmQueue\Controller\Plugin;

use SlmQueue\Service\BeanstalkInterface;

class Queue
{
    protected $beanstalk;

    public function __construct(BeanstalkInterface $beanstalk)
    {
        $this->beanstalk = $beanstalk;
    }

    /**
     * Add job to the beanstalk queue
     *
     * @param Job    $job      Job to be executed
     * @param int    $priority Priority for this job
     * @param int    $delay    Delay for this given job
     * @param int    $ttr      Time to run for this job
     */
    public function add(Job $job, $priority = null, $delay = null, $ttr = null)
    {
        return $this->beanstalk->put($job, $priority, $delay, $ttr);
    }
}

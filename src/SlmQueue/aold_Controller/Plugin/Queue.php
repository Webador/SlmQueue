<?php

namespace SlmQueue\Controller\Plugin;

use SlmQueue\Job\JobInterface;
use SlmQueue\Service\BeanstalkInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Queue extends AbstractPlugin
{
    /**
     * @var BeanstalkInterface
     */
    protected $beanstalk;

    /**
     * @param BeanstalkInterface $beanstalk
     */
    public function __construct(BeanstalkInterface $beanstalk)
    {
        $this->beanstalk = $beanstalk;
    }

    /**
     * Add job to the beanstalk queue
     *
     * @param JobInterface $job      Job to be executed
     * @param int          $priority Priority for this job
     * @param int          $delay    Delay for this given job
     * @param int          $ttr      Time to run for this job
     */
    public function add(JobInterface $job, $priority = null, $delay = null, $ttr = null)
    {
        return $this->beanstalk->put($job, $priority, $delay, $ttr);
    }
}

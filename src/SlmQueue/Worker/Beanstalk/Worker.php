<?php

namespace SlmQueue\Worker\Beanstalk;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\AbstractWorker;

/**
 * BeanstalkWorker
 */
class Worker extends AbstractWorker
{
    /**
     * {@inheritDoc}
     */
    public function processJob(JobInterface $job, QueueInterface $queue)
    {
        try {
            $job->execute();
            $queue->delete($job);
        } catch(ReleasableException $exception) {

        }
    }
}

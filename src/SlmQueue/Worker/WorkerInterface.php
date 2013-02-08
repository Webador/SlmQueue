<?php

namespace SlmQueue\Worker;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;

/**
 * A worker is responsible for processing jobs. Basically, it pops a job and execute it, until the queue is
 * empty or some criterias (like max runs or max memory) are reached
 */
interface WorkerInterface
{
    /**
     * Process jobs in the queue identified by its name
     *
     * @param  string $queueName
     * @return void
     */
    public function processQueue($queueName);

    /**
     * Process a job that comes from the given queue
     *
     * @param  JobInterface   $job
     * @param  QueueInterface $queue
     * @return void
     */
    public function processJob(JobInterface $job, QueueInterface $queue);
}

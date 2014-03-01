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
     * Process jobs in the queue identified by its name. Some queuing systems accept various options when
     * popping jobs, so you can set the options array. Those options depends on the concrete worker
     *
     * @param  string $queueName
     * @param  array  $options
     * @return int How many jobs were processed
     */
    public function processQueue($queueName, array $options = array());

    /**
     * Process a job that comes from the given queue
     *
     * @param  JobInterface   $job
     * @param  QueueInterface $queue
     * @return int Status of the job
     */
    public function processJob(JobInterface $job, QueueInterface $queue);
}

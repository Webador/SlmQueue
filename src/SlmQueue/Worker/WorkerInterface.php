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
     * Process jobs in the queue identified by its name. Contrary to process queue, this method only works
     * for queue that implements BatchableQueueInterface, and can retrieve multiple jobs at once
     *
     * @param  string $queueName
     * @param  array $options
     * @return int How many jobs were processed
     */
    public function processBatchableQueue($queueName, array $options = array());

    /**
     * Process a job that comes from the given queue
     *
     * @param  JobInterface   $job
     * @param  QueueInterface $queue
     * @return void
     */
    public function processJob(JobInterface $job, QueueInterface $queue);
}

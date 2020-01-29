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
     * Process jobs in the given queue. Some queuing systems accept various options when
     * popping jobs, so you can set the options array. Those options depends on the concrete worker
     */
    public function processQueue(QueueInterface $queue, array $options = []): array;

    /**
     * Process a job that comes from the given queue
     */
    public function processJob(JobInterface $job, QueueInterface $queue): int;
}

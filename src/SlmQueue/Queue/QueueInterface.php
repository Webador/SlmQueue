<?php

namespace SlmQueue\Queue;

use SlmQueue\Job\JobInterface;

/**
 * A queue contains a list of jobs. It can performs various tasks on jobs, like putting a new job,
 * removing it...
 *
 * NOTE : if a given queueing system does not support some of the features (for instance, Beanstalk does not support
 * nor batch push and batch delete), just throw a SlmQueue\Queue\Exception\UnsupportedOperationException
 */
interface QueueInterface
{
    /**
     * Get the name of the queue
     *
     * @return string
     */
    public function getName();

    /**
     * Get the job plugin manager
     *
     * @return \SlmQueue\Job\JobPluginManager
     */
    public function getJobPluginManager();

    /**
     * Push a new job into the queue
     *
     * @param  JobInterface $job
     * @return void
     */
    public function push(JobInterface $job);

    /**
     * Push a batch of new jobs into the queue
     *
     * @param  JobInterface[] $jobs
     * @return void
     */
    public function batchPush(array $jobs);

    /**
     * Pop a job (or multiple jobs) from the queue
     *
     * @return JobInterface|array
     */
    public function pop();

    /**
     * Delete a job from the queue
     *
     * @param  JobInterface $job
     * @return void
     */
    public function delete(JobInterface $job);

    /**
     * Delete a batch of jobs from the queue
     *
     * @param  JobInterface[] $jobs
     * @return void
     */
    public function batchDelete(array $jobs);
}

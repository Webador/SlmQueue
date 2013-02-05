<?php

namespace SlmQueue;

use SlmQueue\Job\JobInterface;

/**
 * A queue contains a list of jobs. It can performs various tasks on jobs, like putting a new job,
 * removing it... SlmQueue provides out of the box two queues: one for Beanstalk and one for Amazon SQS
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
     * Push a new job into the queue
     *
     * @param  JobInterface $job
     * @param  array            $options
     * @return void
     */
    public function push(JobInterface $job, array $options = array());

    /**
     * Push a batch of new jobs into the queue
     *
     * @param  JobInterface[] $jobs
     * @param  array          $options
     * @return mixed
     */
    public function batchPush(array $jobs, array $options = array());

    /**
     * Pop a job from the queue
     *
     * @return JobInterface
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

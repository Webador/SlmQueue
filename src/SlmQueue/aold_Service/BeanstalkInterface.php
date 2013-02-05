<?php

namespace SlmQueue\Service;

use SlmQueue\Job\JobInterface;

interface BeanstalkInterface
{
    /**
     * Reserve/locks a ready job in the watched tube, and return it.
     *
     * The Pheanstalk service returns a data blob so convert that into a class which implements
     * the SlmQueue\Job\JobInterface.
     *
     * @return JobInterface|bool
     */
    public function reserve();

    /**
     * Execute the given job
     *
     * @param  JobInterface $job
     * @return void
     */
    public function execute(JobInterface $job);

    /**
     * Put a job in the queue
     *
     * @param JobInterface $job
     * @param int          $priority
     * @param int          $delay
     * @param int          $ttr
     * @return void
     */
    public function put(JobInterface $job, $priority = 1024, $delay = 0, $ttr = 60);

    /**
     * Permanently delete a job
     *
     * @param  JobInterface $job
     * @return void
     */
    public function delete(JobInterface $job);

    /**
     * Put a reserved job back to the queue
     *
     * @param JobInterface $job
     * @param int           $priority
     * @param int           $delay
     * @return void
     */
    public function release(JobInterface $job, $priority = 1024, $delay = 0);

    /**
     * Put a job in the "buried" state, revived only by "kick" command
     *
     * @param  JobInterface $job
     * @param  int           $priority
     * @return void
     */
    public function bury(JobInterface $job, $priority = 1024);

    /**
     * Kick buried or delayed job into a ready state
     *
     * @param  JobInterface $job
     * @return mixed
     */
    public function kick(JobInterface $job);
}
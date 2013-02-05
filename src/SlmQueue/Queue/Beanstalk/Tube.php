<?php

namespace SlmQueue\Queue\Beanstalk;

use SlmQueue\AbstractQueue;
use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\Exception;

/**
 * From the Beanstalk terminology, a tube is equivalent to a queue. It offers some more features than a basic
 * queue, like bury and kick features
 */
class Tube extends AbstractQueue
{
    /**
     * {@inheritDoc}
     */
    public function push(JobInterface $job, array $options = array())
    {
        // TODO: Implement push() method.
    }

    /**
     * {@inheritDoc}
     * @throws Exception\UnsupportedOperationException
     */
    public function batchPush(array $jobs, array $options = array())
    {
        throw new Exception\UnsupportedOperationException('Beanstalk does not support batch push');
    }

    /**
     * {@inheritDoc}
     */
    public function pop()
    {
        // TODO: Implement pop() method.
    }

    /**
     * {@inheritDoc}
     */
    public function delete(JobInterface $job)
    {
        // TODO: Implement delete() method.
    }

    /**
     * {@inheritDoc}
     * @throws Exception\UnsupportedOperationException
     */
    public function batchDelete(array $jobs)
    {
        throw new Exception\UnsupportedOperationException('Beanstalk does not support batch delete');
    }

    /**
     * Bury a job. When a job is buried, it won't be retrieved from the queue, unless the job is kicked
     *
     * @param  JobInterface $job
     * @return void
     */
    public function bury(JobInterface $job)
    {

    }

    /**
     * Kick a job. This allow to retrieve a buried job from the queue
     *
     * @param  JobInterface $job
     * @return void
     */
    public function kick(JobInterface $job)
    {

    }
}

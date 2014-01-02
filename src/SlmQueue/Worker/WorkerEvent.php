<?php

namespace SlmQueue\Worker;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use Zend\EventManager\Event;

/**
 * WorkerEvent
 */
class WorkerEvent extends Event
{
    /**
     * Various events you can listen to
     */
    const EVENT_PROCESS_QUEUE_PRE  = 'processQueue.pre';
    const EVENT_PROCESS_QUEUE_POST = 'processQueue.post';
    const EVENT_PROCESS_JOB_PRE    = 'processJob.pre';
    const EVENT_PROCESS_JOB_POST   = 'processJob.post';

    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * @var JobInterface|null
     */
    protected $job;

    /**
     * @param QueueInterface $queue
     */
    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @param  JobInterface $job
     * @return void
     */
    public function setJob(JobInterface $job)
    {
        $this->job = $job;
    }

    /**
     * @return JobInterface|null
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return QueueInterface
     */
    public function getQueue()
    {
        return $this->queue;
    }
}

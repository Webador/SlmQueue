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
     * Various events you can subscribe to
     */
    const EVENT_BOOTSTRAP        = 'boostrap';
    const EVENT_FINISH           = 'finish';
    const EVENT_PROCESS_IDLE     = 'idle';
    const EVENT_PROCESS_STATE    = 'state';
    const EVENT_PROCESS          = 'process';

    /**
     * Status for unstarted jobs
     */
    const JOB_STATUS_UNKNOWN             = 0;

    /**
     * Status for successfully finished job
     */
    const JOB_STATUS_SUCCESS             = 1;

    /**
     * Status for job that has failed and cannot be processed again
     */
    const JOB_STATUS_FAILURE             = 2;

    /**
     * Status for job that has failed but can be processed again
     */
    const JOB_STATUS_FAILURE_RECOVERABLE = 4;

    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * @var JobInterface|null
     */
    protected $job;

    /**
     * Result of the processed job.
     * @var int
     */
    protected $result;

    /**
     * Flag indicating we want to exit on the next available occasion
     * @var bool
     */
    protected $exitWorker = false;

    /**
     * @param QueueInterface $queue
     */
    public function __construct(WorkerInterface $target, QueueInterface $queue)
    {
        $this->setTarget($target);

        $this->queue = $queue;
    }

    /**
     * @param  JobInterface $job
     * @return void
     */
    public function setJob(JobInterface $job)
    {
        $this->job = $job;
        $this->setResult(self::JOB_STATUS_UNKNOWN);
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

    /**
     * @param int $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return int|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param boolean $exitWorker
     */
    public function exitWorkerLoop()
    {
        $this->exitWorker = true;
    }

    /**
     * @return boolean
     */
    public function shouldWorkerExitLoop()
    {
        return $this->exitWorker;
    }
}

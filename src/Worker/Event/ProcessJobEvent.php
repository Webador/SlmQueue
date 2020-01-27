<?php

namespace SlmQueue\Worker\Event;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\WorkerInterface;

/**
 * ProcessJobEvent
 */
class ProcessJobEvent extends AbstractWorkerEvent
{

    /**
     * Status for unstarted jobs
     */
    public const JOB_STATUS_UNKNOWN = 0;

    /**
     * Status for successfully finished job
     */
    public const JOB_STATUS_SUCCESS = 1;

    /**
     * Status for job that has failed and cannot be processed again
     */
    public const JOB_STATUS_FAILURE = 2;

    /**
     * Status for job that has failed but can be processed again
     */
    public const JOB_STATUS_FAILURE_RECOVERABLE = 4;

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
     *
     * @var int
     */
    protected $result;

    /**
     * @param WorkerInterface $target
     * @param QueueInterface  $queue
     */
    public function __construct(JobInterface $job, WorkerInterface $target, QueueInterface $queue)
    {
        parent::__construct(self::EVENT_PROCESS_JOB, $target);

        $this->queue = $queue;
        $this->setJob($job);
    }

    /**
     * @return QueueInterface
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param JobInterface $job
     * @return void
     */
    private function setJob(JobInterface $job)
    {
        $this->job = $job;
        $this->setResult(self::JOB_STATUS_UNKNOWN);
    }

    /**
     * @return JobInterface
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param int $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }
}

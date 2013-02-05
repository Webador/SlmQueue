<?php

namespace SlmQueue\Queue\Beanstalk;

use Pheanstalk_Job;
use Pheanstalk_Pheanstalk as Pheanstalk;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\AbstractQueue;
use SlmQueue\Queue\Exception;

/**
 * From the Beanstalk terminology, a tube is equivalent to a queue. It offers some more features than a basic
 * queue, like bury and kick features
 */
class Tube extends AbstractQueue
{
    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;


    /**
     * @param Pheanstalk       $pheanstalk
     * @param JobPluginManager $jobPluginManager
     * @param string           $name
     * @param object|null      $options
     */
    public function __construct(
        Pheanstalk $pheanstalk,
        JobPluginManager $jobPluginManager,
        $name,
        $options = null
    ) {
        $this->pheanstalk = $pheanstalk;
        parent::__construct($jobPluginManager, $name, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function push(JobInterface $job, array $options = array())
    {
        $this->pheanstalk->putInTube(
            $this->getName(),
            json_encode($job),
            isset($options['priority']) ? $options['priority'] : Pheanstalk::DEFAULT_PRIORITY,
            isset($options['delay']) ? $options['delay'] : Pheanstalk::DEFAULT_DELAY,
            isset($options['ttr']) ? $options['ttr'] : Pheanstalk::DEFAULT_TTR
        );
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
        /** @var $job \Pheanstalk_Job */
        $job = $this->pheanstalk->reserveFromTube($this->getName());
        return $this->convertJob($job);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(JobInterface $job)
    {
        $this->pheanstalk->delete($job);
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
     * @param  array        $options
     * @return void
     */
    public function bury(JobInterface $job, array $options = array())
    {
        $this->pheanstalk->bury(
            $job,
            isset($options['priority']) ? $options['priority'] : Pheanstalk::DEFAULT_PRIORITY
        );
    }

    /**
     * Kick a specified number of buried jobs, hence making them "ready" again
     *
     * @param  int $max The maximum jobs to kick
     * @return int Number of jobs kicked
     */
    public function kick($max)
    {
        return $this->pheanstalk->kick($max);
    }

    /**
     * Convert a Pheanstalk_Job to our JobInterface
     *
     * @param  Pheanstalk_Job $pheanstalkJob
     * @return JobInterface
     */
    private function convertJob(Pheanstalk_Job $pheanstalkJob)
    {
        $data = json_decode($pheanstalkJob->getData(), true);

        /** @var $job JobInterface */
        $job  = $this->jobPluginManager->get($data['class']);
        $job->setId($pheanstalkJob->getId())
            ->setContent($data['content']);

        return $job;
    }
}

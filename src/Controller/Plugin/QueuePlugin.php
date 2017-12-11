<?php

namespace SlmQueue\Controller\Plugin;

use SlmQueue\Controller\Exception\QueueNotFoundException;
use SlmQueue\Job\JobInterface;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Queue\QueuePluginManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Queue controller plugin
 */
class QueuePlugin extends AbstractPlugin
{
    /**
     * Plugin manager for queues
     *
     * @var QueuePluginManager
     */
    protected $queuePluginManager;

    /**
     * Plugin manager for jobs
     *
     * @var JobPluginManager
     */
    protected $jobPluginManager;

    /**
     * Current selected queue
     *
     * @var QueueInterface
     */
    protected $queue;

    /**
     * Constructor
     *
     * @param QueuePluginManager $queuePluginManager
     * @param JobPluginManager   $jobPluginManager
     */
    public function __construct(QueuePluginManager $queuePluginManager, JobPluginManager $jobPluginManager)
    {
        $this->queuePluginManager = $queuePluginManager;
        $this->jobPluginManager   = $jobPluginManager;
    }

    /**
     * Invoke plugin and optionally set queue
     *
     * @param  string $name Name of queue when set
     * @return self
     */
    public function __invoke($name = null)
    {
        if (null !== $name) {
            if (!$this->queuePluginManager->has($name)) {
                throw new QueueNotFoundException(
                    sprintf("Queue '%s' does not exist", $name)
                );
            }

            $this->queue = $this->queuePluginManager->get($name);
        }

        return $this;
    }

    /**
     * Push a job by its name onto the selected queue
     *
     * @param  string $name    Name of the job to create
     * @param  mixed $payload  Payload of the job set as content
     * @param  array $options  Push job options
     * @throws QueueNotFoundException If the method is called without a queue set
     * @return JobInterface    Created job by the job plugin manager
     */
    public function push($name, $payload = null, array $options = [])
    {
        $this->assertQueueIsSet();

        $job = $this->jobPluginManager->get($name);
        if (null !== $payload) {
            $job->setContent($payload);
        }

        $this->queue->push($job, $options);
        
        return $job;
    }

    /**
     * Push a job on the selected queue
     *
     * @param JobInterface $job
     * @param array $options Push job options
      * @throws QueueNotFoundException If the method is called without a queue set
     */
    public function pushJob(JobInterface $job, array $options = [])
    {
        $this->assertQueueIsSet();

        $this->queue->push($job, $options);
    }

    /**
     * @throws QueueNotFoundException
     */
    protected function assertQueueIsSet()
    {
        if (null === $this->queue) {
            throw new QueueNotFoundException(
                'You cannot push a job without a queue selected'
            );
        }
    }
}

<?php

namespace SlmQueue\Controller\Plugin;

use SlmQueue\Job\JobPluginManager;
use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Queue\QueueInterface;
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
            $this->queue = $this->queuePluginManager->get($name);
        }

        return $this;
    }

    /**
     * Push a job by its name onto the selected queue
     *
     * @param  string $name    Name of the job to create
     * @param  mixed $payload  Payload of the job set as content
     * @throws QueueNotFoundException If the method is called without a queue set
     * @return JobInterface    Created job by the job plugin manager
     */
    public function push($name, $payload = null)
    {
        if (null === $this->queue) {
            throw new QueueNotFoundException(
                'You cannot push a job without a queue selected'
            );
        }

        $job = $this->jobPluginManager->get($name);
        if (null !== $payload) {
            $job->setContent($payload);
        }

        return $this->queue->push($job);
    }
}

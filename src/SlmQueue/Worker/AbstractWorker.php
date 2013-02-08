<?php

namespace SlmQueue\Worker;

use SlmQueue\Job\JobInterface;
use SlmQueue\Options\WorkerOptions;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Queue\QueuePluginManager;

/**
 * AbstractWorker
 */
abstract class AbstractWorker implements WorkerInterface
{
    /**
     * @var QueuePluginManager
     */
    protected $queuePluginManager;

    /**
     * @var WorkerOptions
     */
    protected $options;


    /**
     * Constructor
     *
     * @param QueuePluginManager $queuePluginManager
     * @param WorkerOptions      $options
     */
    public function __construct(QueuePluginManager $queuePluginManager, WorkerOptions $options)
    {
        $this->queuePluginManager = $queuePluginManager;
        $this->options            = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function processQueue($queueName)
    {
        /** @var $queue QueueInterface */
        $queue = $this->queuePluginManager->get($queueName);
        $count = 0;

        while (true) {
            $job = $queue->pop();

            // The queue may return null if there is no more job, or even false if a timeout was set
            if (!$job instanceof JobInterface) {
                break;
            }

            $this->processJob($job, $queue);
            $count++;

            if ($count === $this->options->getMaxRuns()) {
                break;
            }

            if (memory_get_usage() > $this->options->getMaxMemory() * 1024 * 1024) {
                break;
            }
        }

        return $count;
    }

    /**
     * {@inheritDoc}
     */
    abstract public function processJob(JobInterface $job, QueueInterface $queue);
}

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
    public function processQueue($queueName, array $options = array())
    {
        /** @var $queue QueueInterface */
        $queue = $this->queuePluginManager->get($queueName);
        $count = 0;

        while (true) {
            // Pop operations may return a list of jobs or a single job
            $jobs = $queue->pop($options);

            if (!is_array($jobs)) {
                $jobs = array($jobs);
            }

            foreach ($jobs as $job) {
                // The queue may return null, for instance if a timeout was set
                if (!$job instanceof JobInterface) {
                    return $count;
                }

                $this->processJob($job, $queue);
                $count++;

                // Those are various criterias to stop the queue processing
                if ($count === $this->options->getMaxRuns() || memory_get_usage() > $this->options->getMaxMemory()) {
                    return $count;
                }
            }
        }

        return $count;
    }
}

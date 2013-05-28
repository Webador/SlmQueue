<?php

namespace SlmQueue\Worker;

use SlmQueue\Job\JobInterface;
use SlmQueue\Options\WorkerOptions;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Queue\QueueAwareInterface;

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
     * @var bool
     */
    protected $stopped = false;

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

        // Listen to the signals SIGTERM and SIGINT so that the worker can be killed properly. Note that
        // because pcntl_signal may not be available on Windows, we needed to check for the existence of the function
        if (function_exists('pcntl_signal')) {
            declare(ticks = 1);
            pcntl_signal(SIGTERM, array($this, 'handleSignal'));
            pcntl_signal(SIGINT,  array($this, 'handleSignal'));
        }
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
                    continue;
                }

                // The job might want to get the queue injected
                if ($job instanceof QueueAwareInterface) {
                    $job->setQueue($queue);
                }

                $this->processJob($job, $queue);
                $count++;

                // Those are various criterias to stop the queue processing
                if (
                    $count === $this->options->getMaxRuns()
                    || memory_get_usage() > $this->options->getMaxMemory()
                    || $this->isStopped()
                ) {
                    return $count;
                }
            }
        }

        return $count;
    }

    /**
     * Check if the script has been stopped from a signal
     *
     * @return bool
     */
    public function isStopped()
    {
        return $this->stopped;
    }

    /**
     * Handle the signal
     *
     * @param int $signo
     */
    public function handleSignal($signo)
    {
        switch($signo) {
            case SIGTERM:
            case SIGINT:
                $this->stopped = true;
                break;
        }
    }
}

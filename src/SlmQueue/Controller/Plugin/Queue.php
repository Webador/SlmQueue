<?php

namespace SlmQueue\Controller\Plugin;

use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Queue\QueueInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Queue extends AbstractPlugin
{
    protected $queuePluginManager;
    protected $jobPluginManager;

    protected $queue;

    public function __construct(QueuePluginManager $queuePluginManager, JobPluginManager $jobPluginManager)
    {
        $this->queuePluginManager = $queuePluginManager;
        $this->jobPluginManager   = $jobPluginManager;
    }

    public function __invoke($name = null)
    {
        if (null !== $name) {
            $queue = $this->getQueuePluginManager()->get($name);
            $this->setQueue($queue);
        }

        return $this;
    }

    public function push($name, $payload = null)
    {
        if (null === $this->getQueue()) {
            throw new QueueNotFoundException(
                'You cannot push a job without a queue selected'
            );
        }

        $job = $this->getJobPluginManager()->get($name);
        if (null !== $payload) {
            $job->setContent($payload);
        }

        return $this->getQueue()->push($job);
    }

    protected function setQueue(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    protected function getQueue()
    {
        return $this->queue;
    }

    protected function getQueuePluginManager()
    {
        return $this->queuePluginManager;
    }

    protected function getJobPluginManager()
    {
        return $this->jobPluginManager;
    }
}

<?php

namespace SlmQueue\Strategy;

use SlmQueue\Job\JobInterface;
use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class ProcessQueueStrategy extends AbstractStrategy
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_QUEUE,
            array($this, 'onJobPop'),
            $priority
        );
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_JOB,
            array($this, 'onJobProcess'),
            $priority
        );
    }

    /**
     * @param  WorkerEvent $e
     * @return void
     */
    public function onJobPop(WorkerEvent $e)
    {
        $queue   = $e->getQueue();
        $options = $e->getOptions();
        $job     = $queue->pop($options);

        /** @var AbstractWorker $worker */
        $worker       = $e->getTarget();
        $eventManager = $worker->getEventManager();

        $e->setJob($job);

        // The queue may return null, for instance if a timeout was set
        if (!$job instanceof JobInterface) {
            $eventManager->trigger(WorkerEvent::EVENT_PROCESS_IDLE, $e);

            // make sure the event doesn't propagate or it will still process
            $e->stopPropagation();

            return;
        }

        $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB, $e);
    }

    /**
     * @param  WorkerEvent $e
     * @return void
     */
    public function onJobProcess(WorkerEvent $e)
    {
        $job   = $e->getJob();
        $queue = $e->getQueue();

        /** @var AbstractWorker $worker */
        $worker = $e->getTarget();

        $result = $worker->processJob($job, $queue);
        $e->setResult($result);
    }
}

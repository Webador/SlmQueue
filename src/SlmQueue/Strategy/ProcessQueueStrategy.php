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
            WorkerEvent::EVENT_PROCESS,
            array($this, 'onJobPop'),
            $priority + 1
        );
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS,
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

        // The queue may return null, for instance if a timeout was set
        if (!$job instanceof JobInterface) {
            /** @var AbstractWorker $worker */
            $worker = $e->getTarget();

            $worker->getEventManager()->trigger(WorkerEvent::EVENT_PROCESS_IDLE, $e);

            // make sure the event doesn't propagate or it will still process
            $e->stopPropagation();

            return;
        }

        $e->setJob($job);
    }

    /**
     * @param  WorkerEvent $e
     * @return void
     */
    public function onJobProcess(WorkerEvent $e)
    {
        $job    = $e->getJob();
        $queue  = $e->getQueue();
        /** @var AbstractWorker $worker */
        $worker = $e->getTarget();

        $result = $worker->processJob($job, $queue);
        $e->setResult($result);
    }
}

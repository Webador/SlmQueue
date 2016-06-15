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
            [$this, 'onJobPop'],
            $priority
        );
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_JOB,
            [$this, 'onJobProcess'],
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

        // The queue may return null, for instance if a timeout was set
        if (!$job instanceof JobInterface) {
            $idleEvent = new WorkerEvent($e->getTarget(), $queue);
            $idleEvent->setName(WorkerEvent::EVENT_PROCESS_IDLE);
            $idleEvent->setOptions($options);

            $results = $eventManager->trigger($idleEvent);

            foreach ($results as $returnedWorkerEvent) {
                if ($returnedWorkerEvent instanceof WorkerEvent && $returnedWorkerEvent->shouldExitWorkerLoop()) {
                    $e->exitWorkerLoop();
                    $e->stopPropagation();

                    return $e;
                }
            }

            $e->stopPropagation();

            return;
        }

        $processJobEvent = new WorkerEvent($e->getTarget(), $queue);
        $processJobEvent->setName(WorkerEvent::EVENT_PROCESS_JOB);
        $processJobEvent->setOptions($options);
        $processJobEvent->setJob($job);

        $eventManager->triggerEvent($processJobEvent);
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

        return $e;
    }
}

<?php

namespace SlmQueue\Strategy;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ResponseCollection;
use SlmQueue\Job\JobInterface;
use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\Event\ProcessIdleEvent;
use SlmQueue\Worker\Event\ProcessJobEvent;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;

class ProcessQueueStrategy extends AbstractStrategy
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_QUEUE,
            [$this, 'onJobPop'],
            $priority
        );
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_JOB,
            [$this, 'onJobProcess'],
            $priority
        );
    }

    public function onJobPop(ProcessQueueEvent $processQueueEvent): ?ExitWorkerLoopResult
    {
        /** @var AbstractWorker $worker */
        $worker = $processQueueEvent->getWorker();
        $queue = $processQueueEvent->getQueue();
        $options = $processQueueEvent->getOptions();
        $eventManager = $worker->getEventManager();

        $job = $queue->pop($options);

        // The queue may return null, for instance if a timeout was set
        if (! $job instanceof JobInterface) {
            /** @var ResponseCollection $results */
            $results = $eventManager->triggerEventUntil(
                function ($response) {
                    return $response instanceof ExitWorkerLoopResult;
                },
                new ProcessIdleEvent($worker, $queue)
            );

            $processQueueEvent->stopPropagation();

            if ($results->stopped()) {
                return $results->last();
            }

            return null;
        }

        $eventManager->triggerEvent(new ProcessJobEvent($job, $worker, $queue));

        return null;
    }

    public function onJobProcess(ProcessJobEvent $processJobEvent): void
    {
        $job = $processJobEvent->getJob();
        $queue = $processJobEvent->getQueue();

        /** @var AbstractWorker $worker */
        $worker = $processJobEvent->getWorker();

        $result = $worker->processJob($job, $queue);
        $processJobEvent->setResult($result);
    }
}

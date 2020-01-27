<?php

namespace SlmQueue\Strategy;

use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Worker\Event\BootstrapEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;

class WorkerLifetimeStrategy extends AbstractStrategy
{
    /**
     * The timestamp when the worker has started
     *
     * @var int
     */
    protected $startTime = PHP_INT_MAX;

    /**
     * The maximum amount of seconds the worker may do its work
     *
     * @var int
     */
    protected $lifetime = 3600;

    /**
     * {@inheritDoc}
     */
    protected $state = '0 seconds passed';

    /**
     * @param int $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = (int) $lifetime;
    }

    /**
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_BOOTSTRAP,
            [$this, 'onBootstrap'],
            $priority
        );

        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_QUEUE,
            [$this, 'checkRuntime'],
            -1000
        );

        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_IDLE,
            [$this, 'checkRuntime'],
            -1000
        );

        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_STATE,
            [$this, 'onReportQueueState'],
            $priority
        );
    }

    /**
     * @param BootstrapEvent $event
     */
    public function onBootstrap(BootstrapEvent $event)
    {
        $this->startTime = time();
    }

    /**
     * @param WorkerEventInterface $event
     *
     * @return ExitWorkerLoopResult|null
     */
    public function checkRuntime(WorkerEventInterface $event)
    {
        $now = time();
        $runtime = $now - $this->startTime;
        $this->state = sprintf('%d seconds passed', $runtime);

        if ($runtime >= $this->lifetime) {
            $reason = sprintf('lifetime of %d seconds reached', $this->lifetime);

            return ExitWorkerLoopResult::withReason($reason);
        }

        return null;
    }
}

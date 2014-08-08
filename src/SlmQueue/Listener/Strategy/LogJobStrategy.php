<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;

class LogJobStrategy extends AbstractStrategy
{
    /**
     * @var AdapterInterface
     */
    protected $console;

    public function __construct(AdapterInterface $console)
    {
        $this->console = $console;
    }
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS,
            array($this, 'onLogJobProcessStart'),
            10000
        );
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS,
            array($this, 'onLogJobProcessDone'),
            -1000
        );
    }

    /**
     * @param WorkerEvent $e
     */
    public function onLogJobProcessStart(WorkerEvent $e)
    {
        $job  = $e->getJob();
        $name = $job->getMetadata('name');
        if (null === $name) {
            $name = get_class($job);
        }

        $this->console->write(sprintf('Processing job %s...', $name));
    }

    /**
     * @param WorkerEvent $e
     */
    public function onLogJobProcessDone(WorkerEvent $e)
    {
        $this->console->writeLine('Done!');
    }
}
